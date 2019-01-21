<?php
namespace App\Http\Controllers\Documentos;

use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use Auth;
use DB;
use Storage;

use Jenssegers\Date\Date;
use Yajra\Datatables\Datatables;
use Laracasts\Flash\Flash;
use Zipper;

use App\Models\Documento;
use App\Models\DocumentoVersion;
use App\Models\DocumentoValidacion;
use App\Models\TipoDocumento;
use App\Models\Aviso;
use App\Models\Empresa;

class APIDocumentosController extends \App\Http\Controllers\Controller
{
    // Devuelve la caducidad de un documento a partir de su
    // 'tipo_documento_id'->'tipo_caducidad' y la 'fecha_documento'
    // GET /api/documentos/caducidad?tipo_documento_id=1&fecha_documento=1/01/2016
    public function caducidad(Request $request)
    {
        if (($tipo_documento_id = $request->get('t')) &&
            ($fecha_documento = $request->get('f'))) {
            $tipo = TipoDocumento::findOrFail($tipo_documento_id);

            $fecha_caducidad = Date::createFromFormat('d/m/Y', $fecha_documento);

            switch ($tipo->tipo_caducidad) {
                case 'M':
                    $fecha_caducidad->addMonth();
                    break;

                case 'T':
                    $fecha_caducidad->addMonths(3);
                    break;

                case 'S':
                    $fecha_caducidad->addMonths(6);
                    break;

                case 'A':
                    $fecha_caducidad->addYears(1);
                    break;

                case 'N':
                    $fecha_caducidad = null;
                    break;

                case 'V':
                default:
                    break;
            }

            return response()->json([
                'tipo_caducidad' => $tipo->tipo_caducidad,
                'caducidad' => config('enums.tipos_caducidad')[$tipo->tipo_caducidad],
                // 'fecha_documento' => $fecha_documento,
                'fecha_caducidad' => $fecha_caducidad ? Date::parse($fecha_caducidad)->format('d/m/Y') : '',
                'ambito' => $tipo->ambito,
            ]);
        }
    }

    // Descarga el fichero de un documento
    // GET /api/documentos/download/{id}
    public function download($id)
    {
        $fields = explode(',', $id);
        if (count($fields) == 1) {
            $documento = Documento::find($id);
        } else {
            // Versión específica del documento
            $documento = DocumentoVersion::where('id', '=', $fields[0])->where('version', '=', $fields[1])->first();
        }

        if ($documento->count() == 0) {
            Flash::error('¡No se ha encontrado el documento en la base de datos!');
            return back();
        }

        if (Storage::disk('doc')->exists($documento->filename)) {
            if ($file = Storage::disk('doc')->get($documento->filename)) {
                return Response::make($file, 200, [
                    'Content-Type' => $documento->mime,
                    'Content-Disposition: inline; filename="' . $documento->original_filename . '"'
                ]);
            }
        }

        Flash::error('¡No se ha encontrado el documento en el servidor!');

        return back();
    }

    // Prepara un zip con los documentos que se le pasan y con el título indicado
    // Los ficheros de los documentos y el ZIP van a TMP, cuando acaba la descarga
    // se elimina todo.
    // POST /api/documentos/zip
    public function downloadZip(Request $request)
    {
        // dd($request->all());
        if ($ids = $request->get('ids')) {
            if ($title = $request->get('title')) {
                $dir_name = uniqid();
                Storage::disk('tmp')->makeDirectory($dir_name);
                $documentos = Documento::whereIn('id', $ids);
                foreach ($documentos->get() as $documento) {
                    if (Storage::disk('doc')->exists($documento->filename)) {
                        $file = Storage::disk('doc')->get($documento->filename);
                        Storage::disk('tmp')->put($dir_name . '/' . $documento->original_filename, $file);
                    }
                }
                $folder = storage_path('app/tmp/'.$dir_name);
                $zipFile = $folder . '/' . $title . '.zip';
                Zipper::make($zipFile)->add($folder)->close();

                return response()->download($zipFile)->deleteFileAfterSend(true);
            }
        }

        return $this->returnError('Faltan parámetros');
    }

    // Devuelve las versiones de un documento (para Datatable)
    // GET /api/documentos/versiones/{id}
    public function versiones($id)
    {
        $versiones = DocumentoVersion::select('id', 'version', 'fecha_documento', 'fecha_caducidad', 'notas', 'created_at')
            ->where('id', '=', $id)
            ->orderBy('version', 'DESC');

        $datatable = Datatables::of($versiones)
            ->addColumn('fecha_archivado', function ($version) {
                return Date::parse($version->created_at)->format('d/m/Y H:i:s');
            })
            ->removeColumn('created_at')
            ->addColumn('actions', function ($version) {
                return '<div class="btn-group">' .
                            '<a href="' . route('getfile', $version->id.','.$version->version) . '" target="_blank" class="btn btn-default btn-sm" data-toggle="tooltip" title="Descargar"><i class="fa fa-download text-light-blue"></i></a>' .
                        '</div>';
            })
            ->rawColumns(['actions']);

        return $datatable->make(true);
    }

    // Devuelve las tags del tipo de documento
    // GET /api/documentos/tipo-documento-tags/{tipo_documento_id}
    public function tipoDocumentoTags($tipo_documento_id)
    {
        $tags = [];

        $tipo = TipoDocumento::find($tipo_documento_id);
        if ($tipo) {
            $tags = $tipo->tagListNormalized;
        }

        return compact('tags');
    }

    // Recibe el FORM de la validación del documento y guarda el estado correspondiente
    // de validación del documento
    // POST /api/documentos/valida-documento
    public function documentoValidado(Request $request)
    {
        $fields = $request->all();
        $documento_aprobado = array_key_exists('documento_aprobado', $fields);

        if ($documento_aprobado || array_key_exists('documento_rechazado', $fields) == true) {
            $validacion = new DocumentoValidacion();
            $validacion->documento_id = $fields['val_id'];
            $validacion->documento_version = $fields['val_version'];
            $validacion->fecha_revision = Date::now();
            $validacion->usuario_id = Auth::user()->id;
            $validacion->aprobado = $documento_aprobado;
            $validacion->notas = $fields['val_notas_validacion'];
            $validacion->save();

            $documento = Documento::find($validacion->documento_id);
            if ($documento) {
                // Detectamos si se aprueba después de haber sido aprobado, para AVISAR
                $avisar_aprobado = ($documento_aprobado && ($documento->validacion_id != null));
                $documento->validacion_id = $validacion->id;
                $documento->save();

                // AVISO
                if (! $documento_aprobado || $avisar_aprobado) {
                    // No se envían avisos por documentos de la empresa principal
                    if ($ambito_doc = $documento->ambito()) {
                        if ($ambito_doc != 'EMP' && $ambito_doc != 'CEN') {
                            // Empresa contratista responsable del documento
                            $empresa_id = null;
                            $text_suffix = '';
                            switch ($ambito_doc) {
                                case 'CTA':
                                    $empresa_id = $documento->empresa()->id;
                                    $url = route('empresas.edit', $empresa_id) . '#t2';
                                    break;
                                case 'TRA':
                                    $tra = $documento->trabajador()->first();
                                    $empresa_id = $tra->empresa_id;
                                    $tab = '';
                                    switch ($tra->pivot->tipo_documento_trabajador) {
                                        case 'FOR':
                                            $tab = '#t2';
                                            break;
                                        case 'INF':
                                            $tab = '#t3';
                                            break;
                                        case 'EPI':
                                            $tab = '#t4';
                                            break;
                                        case 'VIS':
                                            $tab = '#t5';
                                            break;
                                        case 'OTR':
                                            $tab = '#t6';
                                            break;
                                    }
                                    $url = route('trabajadores.edit', $tra->id) . $tab;
                                    $text_suffix = ' del trabajador <strong>' . $tra->nombreCompleto(true) . '</strong>';
                                    break;
                                case 'MAQ':
                                    $maq = $documento->maquina()->first();
                                    $empresa_id = $maq->empresa_id;
                                    $url = route('maquinas.edit', $maq->id);
                                    $text_suffix = ' de la máquina <strong>' . $maq->nombreMaquina(true) . '</strong>';
                                    break;
                            }
                            if ($empresa_id) {
                                $contratista = Empresa::find($empresa_id);
                                if ($contratista) {
                                    $word = $documento_aprobado ? 'aprobado' : 'rechazado';
                                    $color = $documento_aprobado ? 'green' : 'red';
                                    $motivo = $documento_aprobado ? "" : " por <em>'$validacion->notas'</em>";
                                    $text = "El documento <strong>'$documento->nombre' (#$documento->id)</strong>$text_suffix ha sido <span class=\"text-$color\"><strong>$word</strong></span>$motivo";
                                    $users = $contratista->personas_contacto()->pluck('user_id');
                                    Aviso::createAviso($text, $url, $users);
                                }
                            }
                        }
                    }
                }

                return $this->returnSuccess('El documento se ha marcado como <strong>' . ($documento_aprobado==true ? 'APROBADO' : 'RECHAZADO') . '</strong>.');
            } else {
                return $this->returnError("¡El documento #$validacion->documento_id no existe!");
            }
        }

        return $this->returnError('¡Error en la petición al servidor para la validación del documento!');
    }

    // Devuelve los datos de un documento para editarlo
    // GET /api/documentos/data/{id}
    public function getDocumentoData(Request $request, $id)
    {
        $documento = Documento::find($id);
        if ($documento == null) {
            return $this->returnError("¡El documento #$id no existe!");
        }

        $tipo = TipoDocumento::find($documento->tipo_documento_id);
        if ($documento == null) {
            return $this->returnError("¡El tipo de documento #$documento->tipo_documento_id no existe!");
        }

        // Esto es un 'apaño' muy malo por culpa del campo horas_formacion en
        // trabajadores
        $horas_formacion = null;
        if ($tipo->ambito == 'TRA') {
            $doc = DB::table('trabajadores_doc')->where('documento_id', '=', $id)->get();
            $horas_formacion = $doc[0]->horas_formacion;
        }

        // Status de la validación
        $status_validacion = $documento->statusValidacion();

        return [
            'result' => 'success',
            'data' => [
                'id' => $id,
                'nombre' => $documento->nombre,
                'version' => $documento->version,
                'fecha_documento' => $documento->fecha_documento,
                'fecha_caducidad' => $documento->fecha_caducidad,
                'notas' => $documento->notas,
                'original_filename' => $documento->original_filename,
                'tipo_documento_nombre' => $tipo->nombre,
                'horas_formacion' => $horas_formacion,
                'ambito' => $tipo->ambito,
                'status_validacion' => $status_validacion
            ]
        ];
    }
}
