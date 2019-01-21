<?php
namespace App\Http\Controllers\Contratos;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Auth;
use Validator;
use DB;
use Response;

use Laracasts\Flash\Flash;
use Jenssegers\Date\Date;
use Yajra\Datatables\Datatables;
use Form;

use App\Http\Controllers\Documentos\DocumentosBaseTrait;
use App\Http\Controllers\Documentos\TiposDocumentosTrait;
use App\Http\Controllers\Base\UserMethodsTrait;
use App\Http\Controllers\Base\EmailsTrait;

use App\User;
use App\Models\Contrato;
use App\Models\TipoContrato;
use App\Models\TipoDocumento;
use App\Models\Centro;
use App\Models\Trabajador;
use App\Models\Empresa;
use App\Models\Aviso;

class ContratosController extends \App\Http\Controllers\Base\BaseController
{
    use ContratosDocumentacionTrait, ContratosContratistasTrait,
        DocumentosBaseTrait, TiposDocumentosTrait,
        UserMethodsTrait, EmailsTrait;

    // TiposDocumentosTrait
    protected $tdt_pivot_table_name = 'contratos_doc_requerida';
    protected $tdt_pivot_table_main_id_name = 'contrato_id';

    public function __construct()
    {
        parent::__construct(Contrato::class, 'contratos');
    }

    // public function index()

    public function create(Request $request)
    {
        $tipos = $this->getListaTiposContratos();

        $responsables = $this->empleados_empresa('responsable');
        $tecnicos = $this->empleados_empresa('tecnico');

        return parent::__create($request, compact('tipos', 'responsables', 'tecnicos', 'return_to'));
    }

    public function store(Request $request)
    {
        $contrato = parent::__store_create_record($request);
        // Error de validación
        if ($contrato == false) {
            return redirect()->back()
                             ->with('errors', $this->validator_instance->errors())
                             ->withInput();
        }
        // Avisos
        Aviso::createAviso(
            'Has sido añadido al nuevo contrato ' . $contrato->getNombreAvisos() . '.',
            route('contratos.edit', $contrato->id) . '#t2',
            $contrato->getUsuariosEmpresaPrincipal()
        );

        // Añadir los tipos de documentos del Tipo de Contrato
        $msg = $this->addDocumentacionRequeridaContrato($contrato);

        return parent::__store_return($msg);
    }

    public function edit(Request $request, $id)
    {
        $contrato = $this->currentContrato($id);

        if ($contrato->tipo_contrato_id == null) {
            $tipos = $this->getListaTiposContratos();
        } else {
            $tipos = TipoContrato::pluck('nombre', 'id');
        }

        $responsables = $this->empleados_empresa('responsable');
        $tecnicos = $this->empleados_empresa('tecnico');
        // Contrato
        $datos_tra = $this->getDatosTrabajador($contrato->responsable_contrato_id);
        $responsable_contrato_nombre = $datos_tra[0];
        $responsable_contrato_contacto = $datos_tra[1];
        $datos_tra = $this->getDatosTrabajador($contrato->tecnico_encargado_id);
        $tecnico_encargado_contacto = $datos_tra[1];
        $datos_tra = $this->getDatosTrabajador($contrato->tecnico_encargado2_id);
        $tecnico_encargado2_contacto = $datos_tra[1];
        $datos_tra = $this->getDatosTrabajador($contrato->tecnico_prl_id);
        $tecnicoprl_nombre = $datos_tra[0];
        $tecnicoprl_contacto = $datos_tra[1];
        $datos_tra = $this->getDatosTrabajador($contrato->coordinador_cap_id);
        $coordinador_cap_contacto = $datos_tra[1];
        $datos_tra = $this->getDatosTrabajador($contrato->tecnico_averias_id);
        $tecnico_averias_contacto = $datos_tra[1];
        // Centros
        $num_centros = $contrato->centros()->count();
        $centros_add_route = route('contratos.addCentros');
        $centros_sel_html = Form::fhSelect('centro_id_documento', [], null, 'Centro', 'Seleccione el Centro...', true, [], 10);
        $centros_sel_html = str_replace(array("\r", "\n", "\t", "  "), '', $centros_sel_html);
        // Doc. Requerida
        $tipos_documentos_add_route = route('contratos.addTiposDocumento');
        // Doc. Empresa
        $tipos_documentos = TipoDocumento::whereIn('ambito', ['EMP', 'CEN'])->pluck('nombre', 'id');
        // Contratistas
        $contratistas_contrato = $contrato->contratistas()->pluck('razon_social', 'id');
        $num_subcontratistas = $contrato->subcontratistas()->count();
        // Documentos privados
        $doc_privados = $contrato->getMedia();

        return parent::__edit(
            $request,
            $id,
            compact(
                'tipos',
                'responsables',
                'tecnicos',
                'responsable_contrato_nombre',
                'responsable_contrato_contacto',
                'tecnicoprl_nombre',
                'tecnicoprl_contacto',
                'tecnico_encargado_contacto',
                'tecnico_encargado2_contacto',
                'coordinador_cap_contacto',
                'tecnico_averias_contacto',
                'centros_add_route',
                'num_centros',
                'centros_sel_html',
                'doc_privados',
                'tipos_documentos_add_route',
                'tipos_documentos',
                'contratistas_contrato',
                'num_subcontratistas'
            )
        );
    }

    public function update(Request $request, $id)
    {
        // Contrato antes de modificarlo
        $c_old = Contrato::find($id);
        if ($c_old == null) {
            Flash::error("¡No existe el contrato que se pretende actualizar!");
            return redirect()->back();
        }

        // Trabajadores de la empresa principal (para Avisos)
        $tep_old = $c_old->getTrabajadoresEmpresaPrincipal();

        $contrato = parent::__update_save_record($request, $id);
        // Error de validación
        if ($contrato == false) {
            return redirect()->back()
                             ->with('errors', $this->validator_instance->errors())
                             ->withInput();
        }

        // Avisos
        $trabajadores_aviso = [];
        $tep = $contrato->getTrabajadoresEmpresaPrincipal();
        foreach ($tep_old as $key => $value) {
            if ($tep[$key] != $value) {
                $trabajadores_aviso[] = $tep[$key];
            }
        }
        $usuarios_aviso = Trabajador::whereIn('id', array_filter($trabajadores_aviso))->pluck('user_id')->toArray();
        Aviso::createAviso(
            'Has sido añadido al contrato ' . $contrato->getNombreAvisos() . '.',
            route('contratos.edit', $contrato->id) . '#t2',
            $usuarios_aviso
        );

        // Si viene el tipo de contrato por primera vez...
        // añadimos la documentación requerida al contrato
        $msg = "";
        if ($c_old->tipo_contrato_id == null) {
            $msg = $this->addDocumentacionRequeridaContrato($contrato);
        }

        return parent::__update_return($msg);
    }

    // public function remove($id)

    public function rowsData(Request $request)
    {
        $contratos = Contrato::leftJoin('trabajadores', 'contratos.responsable_contrato_id', '=', 'trabajadores.id')
                             ->select([ 'contratos.id', 'contratos.nombre', 'contratos.referencia', 'contratos.fecha_firma',
                                    'contratos.fecha_inicio_obras', 'contratos.fecha_fin_obras', 'contratos.tipo_contrato_id',
                                    DB::raw("CONCAT(trabajadores.apellidos, ', ', trabajadores.nombre, ' (#', trabajadores.id, ')') AS responsable"),
                                ])
                             ->where('contratos.activo', '=', true);
        // Limitamos los contratos que puede ver/acceder el contratista
        if (Auth::user()->isExterno()) {
            $contratos = $contratos->leftJoin('contratos_contratistas', 'contratos.id', '=', 'contratos_contratistas.contrato_id')
                                   ->where('contratos_contratistas.empresa_id', '=', Auth::user()->empresa_id)
                                   ->orWhere('contratos_contratistas.subcontratista_id', '=', Auth::user()->empresa_id)
                                   ->distinct();
        }
        $datatable = Datatables::of($contratos)
            ->addColumn('fecha_firma_raw', function ($contrato) {
                if ($contrato->fecha_firma != null) {
                    return Date::createFromFormat('d/m/Y', $contrato->fecha_firma);
                } else {
                    return ' ';
                }
            })
            ->addColumn('fecha_inicio_raw', function ($contrato) {
                if ($contrato->fecha_inicio_obras != null) {
                    return Date::createFromFormat('d/m/Y', $contrato->fecha_inicio_obras);
                } else {
                    return ' ';
                }
            })
            ->addColumn('fecha_fin_raw', function ($contrato) {
                if ($contrato->fecha_fin_obras != null) {
                    return Date::createFromFormat('d/m/Y', $contrato->fecha_fin_obras);
                } else {
                    return ' ';
                }
            })
            ->addColumn('tipo_contrato_column', function ($contrato) {
                if ($contrato->tipo_contrato_id != null) {
                    $nombre = TipoContrato::getNombreTipoContrato($contrato->tipo_contrato_id);
                    return $contrato->tipo_contrato_id .
                        '&nbsp;&nbsp;<i class="fa fa-plus-circle text-dark-gray" data-toggle="tooltip" data-placement="right" data-title="' .
                            $nombre . '"></i>';
                } else {
                    return ' ';
                }
            })
            ->addColumn('contratista', function ($contrato) {
                $str = '';
                $contratistas = $contrato->contratistas();
                if ($contratistas->count() > 0) {
                    // El contratista/sub sólo puede verse él como contratista del contrato
                    if (Auth::user()->isExterno()) {
                        $str = Empresa::getNombreEmpresa(Auth::user()->empresa_id);
                        if ($contratistas->count() > 1) {
                            $str .= ' y otros...';
                        }
                    } else {
                        $nombres = '';
                        $firstTime = true;
                        foreach ($contratistas->get() as $contratista) {
                            if ($firstTime == true) {
                                $str = $contratista->displayName();
                                if ($contratistas->count() > 1) {
                                    $str .= ' &nbsp;&nbsp;<i class="fa fa-plus-circle text-dark-gray" data-toggle="tooltip" data-placement="top" data-html="true" data-title="';
                                }
                                $firstTime = false;
                            } else {
                                $str .= $contratista->displayName() . '<br />';
                            }
                        }
                        if ($contratistas->count() > 1) {
                            $str .= '"></i>';
                        }
                    }
                }
                return $str;
            })
            ->addColumn('status_doc', function ($contrato) {
                return $this->getDocStatusColumn($contrato->statusDocContrato());
            })
            ->rawColumns(['tipo_contrato_column','contratista','status_doc']);

        return $datatable->make(true);
    }

    protected function validator(array $data, $isUpdate = false)
    {
        if ($isUpdate && isset($data['importe_contrato']) && isset($data['notas_privadas'])) {
            $rules = [];
            $fields_names = [];
        } else {
            $rules = [
                'referencia' => 'required|max:30|' . ($isUpdate ? 'unique:contratos,referencia,'.$data['id'] : 'unique:contratos'),
                'nombre' => 'required|max:255',
                'responsable_contrato_id' => 'required|min:1',
                'tecnico_prl_id' => 'required|min:1',
                'fecha_firma' => 'date_format:d/m/Y',
                'fecha_inicio_obras' => 'date_format:d/m/Y',
                'fecha_fin_obras' => 'date_format:d/m/Y',
            ];
            $fields_names = [
                'nombre' => 'Nombre',
                'referencia' => 'Referencia',
                'responsable_contrato_id' => 'Responsable del Contrato',
                'tecnico_prl_id' => 'Técnico P.R.L.',
                'fecha_firma' => 'Fecha Firma',
                'fecha_inicio_obras' => 'Fecha Inicio Contrato',
                'fecha_fin_obras' => 'Fecha Fin Contrato',
            ];
        }

        $validator = Validator::make($data, $rules);
        $validator->setAttributeNames($fields_names);

        return $validator;
    }


    // *************************************************************************
    //  EMPLEADOS EMPRESA PRINCIPAL
    // *************************************************************************
    private function empleados_empresa($roleSlug)
    {
        $role_id = User::findRoleBySlug($roleSlug)->id;
        $_empleados = Trabajador::whereHas('user', function ($query) use ($role_id) {
            $query->leftJoin('role_user', 'role_user.user_id', '=', 'users.id')
                                          ->where('role_user.role_id', '=', $role_id);
        })
                                ->where('activo', '1')
                                ->orderBy('apellidos')
                                ->get();
        $empleados = [];
        foreach ($_empleados as $empleado) {
            $empleados[$empleado->id] = $empleado->nombreCompleto();
        }

        return $empleados;
    }

    // Datos de contacto de los trabajadores de la EMP. PPAL. en el contrato
    private function getDatosTrabajador($trabajador_id)
    {
        $datos = null;
        $trabajador = Trabajador::find($trabajador_id);
        if ($trabajador != null) {
            $datos[] = $trabajador->nombreCompleto();
            $contacto = '';
            if ($trabajador->email != null) {
                $contacto .= 'Email: ' . $trabajador->email . '    ';
            }
            if ($trabajador->telefono != null) {
                $contacto .= 'Teléfono: ' . $trabajador->telefono;
            }
            $datos[] = $contacto;
        }

        return $datos;
    }


    // *************************************************************************
    // CENTROS
    // *************************************************************************
    // Devuelve lista de Centros del contrato
    public function centrosData(Request $request)
    {
        $contrato = $this->currentContrato();

        // Lista simple de centros para el diálogo de añadir documentos de la empresa principal
        if ($request->get('s') == 'true') {
            $centros = $contrato->centros()->pluck('nombre', 'id');
            $array = [];
            foreach ($centros as $key => $value) {
                $arr = ['id' => $key, 'text' => $value];
                $array[] = $arr;
            }
            return response()->json($array);
        }

        $centros = $contrato->centros()->get();
        $datatable = Datatables::of($centros)->setRowId('id');
        if (Auth::user()->can('contratos.update')) {
            $datatable = $datatable->addColumn('actions', function ($centro) {
                return  '<div class="btn-group">' .
                            '<a class="btn btn-default btn-sm" data-id="'.$centro->id.'"><i class="fa fa-times text-red"></i></a>' .
                        '</div>';
            })->rawColumns(['actions']);
        }

        return $datatable->make(true);
    }

    public function addCentros(Request $request)
    {
        if ($ids = $request->get('ids')) {
            $ids_array = explode(',', $ids);
            $contrato = $this->currentContrato();
            if ($contrato) {
                if (count($ids_array) == 1) {
                    $msg = 'Se ha añadido el <strong>Centro de Trabajo</strong>';
                } else {
                    $msg = 'Se han añadido los <strong>Centros de Trabajo</strong>';
                }
                $contrato->centros()->sync($ids_array, false);
                // Añadimos los documentos de los centros al contrato
                if ($contrato->tipo_contrato_id != null) {
                    $res = $this->addDocumentacionRequeridaCentros($contrato);
                    if ($res && ($res > 0)) {
                        $msg .= ' (y sus <strong>documentos</strong>) al contrato';
                    }

                    return $this->returnSuccess($msg . '.');
                }
            }
        }

        return $this->returnError('¡Ha ocurrido un error y el/los Centro(s) de Trabajo no se ha(n) podido añadir!');
    }

    public function detachCentros($id)
    {
        $contrato = $this->currentContrato();
        $contrato->centros()->detach([$id]);
        // Quitar documentos del centro del contrato
        $centro = Centro::find($id);
        $docs_array = $centro->documentos()->pluck('id')->toArray();
        $contrato->documentos()->detach($docs_array);

        return $this->returnSuccess('Se ha quitado el <strong>Centro de Trabajo</strong> (y sus <strong>documentos</strong>) de este contrato.');
    }


    // *************************************************************************
    // DOCUMENTOS
    // *************************************************************************

    // Añade, Modifica o crea nueva Versión de un documento de la empresa principal
    // URL: POST contratos/documentos/add
    public function addDocumento(Request $request)
    {
        if ($request['tipo_documento_ambito'] == 'CEN') {
            $model = Centro::class;
            $editingId = $request['centro_id_documento'];
        } else {
            $model = Empresa::class;
            $editingId = 0;
        }
        // Llamamos al método en el trait DocumentosBaseTrait
        return $this->__addDocumento($request, $model, $editingId);
    }

    // Devuelve los datos de un documento de la empresa principal para editarlo
    // URL: contratos/documentos/{id}/data
    public function getDocumentoData(Request $request, $id)
    {
        // Llamamos al método en el trait DocumentosBaseTrait
        return $this->__getDocumentoData($request, $id, Empresa::class);
    }

    public function addDocumentoPrivado(Request $request)
    {
        $contrato = $this->currentContrato();

        if (isset($request['add-documento-privado-file'])) {
            if (isset($request['add-documento-privado-nombre'])) {
                $contrato->addMediaFromRequest('add-documento-privado-file')
                         ->preservingOriginal()
                         ->usingName($request['add-documento-privado-nombre'])
                         ->toMediaLibrary();
            } else {
                $contrato->addMediaFromRequest('add-documento-privado-file')
                         ->preservingOriginal()
                         ->toMediaLibrary();
            }
            Flash::success('Documento privado guardado.');
        }

        return redirect()->to(app('url')->previous().'#t0');
    }

    public function getDocumentoPrivado($idx)
    {
        $contrato = $this->currentContrato();
        $docs = $contrato->getMedia();
        if (isset($docs[$idx])) {
            $doc = $docs[$idx];
            if (file_exists($doc->getPath())) {
                if ($file = file_get_contents($doc->getPath())) {
                    return Response::make($file, 200, [
                        'Content-Type' => $doc->mime_type,
                        'Content-Disposition: inline; filename="' . $doc->file_name . '"'
                    ]);
                }
            }
        }

        Flash::error('¡No se ha encontrado el documento en el servidor!');
        return redirect()->to(app('url')->previous().'#t0');
    }








    private function getListaTiposContratos()
    {
        $array = [];

        $tipos = TipoContrato::where('activo', true)->get();
        foreach ($tipos as $tipo) {
            $array[$tipo->id] = $tipo->nombre . '|' . $tipo->notas;
        }

        return $array;
    }

    // Añade toda la documentación requerida tras definir el tipo de contrato:
    //      empresa principal, centros de trabajo, contratistas
    private function addDocumentacionRequeridaContrato($contrato)
    {
        $msg = "";

        if ($contrato->tipo_contrato_id != null) {
            $res = $contrato->addDocumentacionRequerida();
            if ($res) {
                $msg = "Se ha añadido la <strong>documentación requerida</strong> para el Tipo de Contrato especificado.";
                // Añadir los documentos de la empresa principal que correspondan
                // a la documentación requerida del contrato. Si son obligatorios
                $res = $contrato->addDocumentosRequeridos('EMP', Empresa::find(0), 'empresa_id');
                $msg .= $this->getResultMsgAddDocumentacionRequerida($res, 'la empresa principal');
                // Idem para los centros de trabajo
                $res = $this->addDocumentacionRequeridaCentros($contrato);
                $msg .= $this->getResultMsgAddDocumentacionRequerida($res, 'los centros de trabajo');
                // Idem para los contratistas
                $res = $this->addDocumentacionRequeridaContratistas($contrato);
                $msg .= $this->getResultMsgAddDocumentacionRequerida($res, 'los contratistas');
            } else {
                $msg = "¡No se ha podido añadir la <strong>documentación requerida</strong> para el Tipo de Contrato especificado!";
            }
        }

        return $msg;
    }

    // Añade al contrato toda la documentación requerida existente de los centros
    private function addDocumentacionRequeridaCentros($contrato)
    {
        $centros = $contrato->centros();
        if ($centros->count() == 0) {
            return null;
        }

        $res = 0;
        foreach ($centros->get() as $centro) {
            $res += $contrato->addDocumentosRequeridos('CEN', $centro, 'centro_id');
        }

        return $res;
    }

    // Añade al contrato toda la documentación requerida existente de los contratistas
    private function addDocumentacionRequeridaContratistas($contrato)
    {
        $contratistas = $contrato->contratistas();
        if ($contratistas->count() == 0) {
            return null;
        }

        $res = 0;
        foreach ($contratistas->get() as $contratista) {
            $res += $contrato->addDocumentosRequeridos('CTA', $contratista, 'empresa_id');
        }

        return $res;
    }

    // Devuelve el mensaje adecuado tras añadir documentación requerida
    private function getResultMsgAddDocumentacionRequerida($count, $item)
    {
        $msg = "";

        if ($count) {
            if ($count == 1) {
                $msg = "<br />Se ha añadido <strong>1 documento de $item</strong> que coincide con la documentación requerida.";
            } elseif ($count > 1) {
                $msg = "<br />Se han añadido <strong>$count documentos de $item</strong> que coinciden con la documentación requerida.";
            }
        } else {
            $msg = "<br />¡No de ha podido añadir los <strong>documentos de $item</strong> que coinciden con la documentación requerida!";
        }

        return $msg;
    }
}
