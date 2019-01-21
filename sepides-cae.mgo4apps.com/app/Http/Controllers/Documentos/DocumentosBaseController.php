<?php
namespace App\Http\Controllers\Documentos;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;

use Yajra\Datatables\Datatables;

use App\Models\Documento;
use App\Models\TipoDocumento;

class DocumentosBaseController extends \App\Http\Controllers\Base\BaseController
{
    use DocumentosBaseTrait;

    public function __construct($model = null, $base_route = null, $model_display_name = null, $views_path = null)
    {
        parent::__construct($model, $base_route, $model_display_name, $views_path);
    }

    public function __edit(Request $request, $id, $external_data = null)
    {
        if (!isset($external_data['ambito'])) {
            return "Se te ha olvidado el ámbito!!";
        }

        $tipos_documentos = TipoDocumento::where('ambito', '=', $external_data['ambito'])->pluck('nombre', 'id');

        $data = compact('tipos_documentos');

        if ($external_data != null) {
            $data = array_merge($external_data, $data);
        }

        return parent::__edit($request, $id, $data);
    }


    // ************************************************************************
    // DOCUMENTOS
    // ************************************************************************

    // Añade o Modifica un documento, o Crea nueva versión de un documento
    // URL: POST /documentos/add
    public function addDocumento(Request $request)
    {
        // Llamamos al método en el trait DocumentosBaseTrait
        return $this->__addDocumento($request, $this->model, $this->getEditingId());
    }

    // Devuelve la lista de documentos
    // URL: ../documentos/data
    public function documentosData(Request $request)
    {
        $model = $this->model;
        $instance = $model::find($this->getEditingId());

        $documentos = $instance->documentos()->where('activo', '=', true);

        // TRABAJADORES
        if ($tipo_documento_trabajador = $request->get('tdt')) {
            $documentos = $documentos->wherePivot('tipo_documento_trabajador', '=', $tipo_documento_trabajador);
        }

        return $this->generateDatatable($request, $documentos);
    }

    // Devuelve los datos de un documento para editarlo
    // URL: ../documentos/{id}/data
    public function getDocumentoData(Request $request, $id)
    {
        // Llamamos al método en el trait DocumentosBaseTrait
        return $this->__getDocumentoData($request, $id, $this->model);
    }

    // Archiva un documento, es decir, lo marca como inactivo
    // URL: ../documentos/remove/{id}
    public function detachDocumento($id)
    {
        if ($documento = Documento::find($id)) {
            $documento->activo = false;
            $documento->save();

            return [
                'result' => 'success',
                'msg' => 'Documento archivado.'
            ];
        }

        return [
            'result' => 'error',
            'msg' => '¡No se ha podido archivar el documento!'
        ];
    }


    // ************************************************************************
    //  DATATABLE
    // ************************************************************************

    private function generateDatatable(Request $request, $documentos)
    {
        $datatable = $this->defaultDatatable($documentos);
        return $datatable->make(true);
    }

    private function defaultDatatable($documentos)
    {
        $datatable = Datatables::of($documentos)
                                ->addColumn('status_caducidad', function ($documento) {
                                    $tipo = TipoDocumento::findOrFail($documento->tipo_documento_id);
                                    if ($tipo->tipo_caducidad == 'N') {
                                        return ' ';
                                    } else {
                                        $data = 'Tipo Caducidad: <strong>' . config('enums.tipos_caducidad')[$tipo->tipo_caducidad] . '</strong>';
                                        $data .= '<br />Fecha Caducidad: <strong>' . $documento->fecha_caducidad . '</strong>';
                                        return $this->getCaducidadStatusColumn($documento->statusCaducidad()) . '&nbsp;&nbsp' .
                                            '<i class="fa fa-plus-circle text-dark-gray" data-toggle="tooltip" data-placement="right" data-html="true" data-title="' .
                                                $data . '"></i>';
                                    }
                                })
                                ->addColumn('status_validacion', function ($documento) {
                                    return $this->getValidacionStatusColumn($documento->statusValidacion(), $documento->validacion());
                                })
                                ->addColumn('tags', function ($documento) {
                                    return $this->getTagsColumn($documento);
                                })
                                ->addColumn('actions', function ($documento) {
                                    return $this->actionsColumn($documento);
                                })
                                ->rawColumns(['status_caducidad','status_validacion','tags','actions']);

        return $datatable;
    }

    private function actionsColumn($documento)
    {
        $versiones_text = 'Documento sin versiones anteriores';
        $num_versiones = $documento->versiones()->count();
        if ($num_versiones > 0) {
            $versiones_text = 'Hay ' . $num_versiones . (($num_versiones == 1) ? ' versión anterior' : ' versiones anteriores') . ' del documento';
        }

        $html = '<div class="btn-group">' .
                    '<a href="' . route('getfile', $documento->id) . '" target="_blank" class="btn btn-default btn-sm" data-toggle="tooltip" title="Ver/Descargar"><i class="fa fa-download text-light-blue"></i></a>';
        if (Auth::user()->can('documentos.validar')) {
            $status = $documento->statusValidacion();
            $label = "Validar documento";
            $icon = "thumbs-up text-green";
            if ($status == 1) {
                $label = "Rechazar documento";
                $icon = "thumbs-down text-red";
            } elseif ($status == -1) {
                $label = "Aprobar documento";
            }
            $html .= '<a class="btn btn-default btn-sm" data-action="validate" data-id="'.$documento->id.'" data-toggle="tooltip" title="'.$label.'"><i class="fa fa-'.$icon.'"></i></a>' .
                     '<div class="btn-group">' .
                        '<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>' .
                        '<ul class="dropdown-menu dropdown-menu-right" style="min-width:100px">' .
                            '<li><a data-action="remove" data-id="'.$documento->id.'" data-toggle="tooltip" title="Archivar">Archivar</a></li>' .
                            '<li><a data-action="versions" data-id="'.$documento->id.'" data-versions=' . $num_versiones . ' data-toggle="tooltip" title="' . $versiones_text . '">Versiones</a></li>' .
                        '</ul>' .
                     '</div>';
        } else {
            $html .= '<a class="btn btn-default btn-sm" data-action="remove" data-id="'.$documento->id.'" data-toggle="tooltip" title="Archivar"><i class="fa fa-times text-red"></i></a>' .
                     '<a class="btn btn-default btn-sm" data-action="versions" data-id="'.$documento->id.'" data-versions=' . $num_versiones . ' data-toggle="tooltip" title="' . $versiones_text . '"><i class="fa fa-archive text-muted"></i></a>';
        }

        return $html . '</div>';
    }
}
