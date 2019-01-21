<?php

namespace App\Http\Controllers\Documentos;

use Illuminate\Http\Request;
use App\Http\Requests;
use Laracasts\Flash\Flash;
use Yajra\Datatables\Datatables;
use App\Models\Documento;
use App\Models\TipoDocumento;

trait TiposDocumentosTrait
{
    // Añadir dos variables como estas al controller que implemente el trait
    // protected $tdt_pivot_table_name = 'tipos_maquinas_doc';
    // protected $tdt_pivot_table_main_id_name = 'tipo_maquina_id';

    // Asocia los tipos de documentos seleccionados con el modelo principal
    public function addTiposDocumento(Request $request)
    {
        if ($ids = $request->get('ids')) {
            $ids_array = explode(',', $ids);
            if ($main_table_id = $this->getEditingId()) {
                $model = $this->model;
                $instance = $model::find($main_table_id);
                $instance->tipos_documentos()->sync($ids_array, false);
                return [
                    'result' => 'success',
                    'msg' => 'Tipos de documento añadidos.'
                ];
            }
        }
        return [
            'result' => 'error',
            'msg' => '¡No se han podido añadir los tipos de documentos!'
        ];
    }

    // Devuelve JSON con los tipos de documentos asociados a un tipo de contrato
    public function tiposDocumentosData(Request $request)
    {
        $tipos = TipoDocumento::join($this->tdt_pivot_table_name, $this->tdt_pivot_table_name . '.tipo_documento_id', '=', 'tipos_documentos.id')
                        ->select([ 'tipos_documentos.*', $this->tdt_pivot_table_name . '.obligatorio' ]);

        if ($main_table_id = $this->getEditingId()) {
            $tipos->where($this->tdt_pivot_table_name . '.' . $this->tdt_pivot_table_main_id_name, '=', $main_table_id);
        }

        return Datatables::of($tipos)
            ->editColumn('ambito', function ($tipo) {
                return config('enums.doc_scopes')[$tipo->ambito];
            })
            ->editColumn('tipo_caducidad', function ($tipo) {
                return config('enums.tipos_caducidad')[$tipo->tipo_caducidad];
            })
            ->addColumn('tags', function ($documento) {
                return $this->getTagsColumn($documento);
            })
            ->addColumn('is_obligatorio', function ($tipo) {
                $html = '<input id="obligatorio_' . $tipo->id . '" name="obligatorio_' . $tipo->id . '" class="tipo_documento_obligatorio" type="checkbox" ';
                if ($tipo->obligatorio == true) {
                    $html .= 'checked ';
                }
                return $html . '/>';
            })
            ->addColumn('actions', function ($tipo) {
                return  '<div class="btn-group">' .
                            '<a class="btn btn-default btn-sm" data-id="' . $tipo->id . '"><i class="fa fa-times text-red"></i></a>' .
                        '</div>';
            })
            ->rawColumns(['tags','is_obligatorio','actions'])
            ->make(true);
    }

    // Cambia el flag 'obligatorio'
    public function cambiaTipoDocumentoObligatorio(Request $request)
    {
        if (($main_table_id = $this->getEditingId()) &&
            ($tipo_documento_id = $request->get('t')) &&
            ($obligatorio = $request->get('o'))) {
            $model = $this->model;
            $instance = $model::find($main_table_id);
            $instance->tipos_documentos()->sync([$tipo_documento_id => ['obligatorio' => ($obligatorio === 'true')]], false);
            return [
                'result' => 'success',
                'msg' => 'Se ha marcado el Tipo de Documento como ' . ($obligatorio === 'false' ? 'NO ' : '') . 'obligatorio.',
            ];
        }
        return [
            'result' => 'error',
            'msg' => '¡No se ha podido modificar la marca de "Obligatorio" del Tipo de Documento!',
        ];
    }

    // Quita el tipo de documento de la asociación
    public function detachTipoDocumento(Request $request, $id)
    {
        if ($main_table_id = $this->getEditingId()) {
            $model = $this->model;
            $instance = $model::find($main_table_id);
            $instance->tipos_documentos()->detach([$id]);
            return [
                'result' => 'success',
                'msg' => 'Se ha quitado el Tipo de Documento.',
            ];
        }
        return [
            'result' => 'error',
            'msg' => '¡Ha ocurrido un error y el Tipo de Documento no se ha podido quitar!',
        ];
    }
}
