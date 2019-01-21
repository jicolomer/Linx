<?php
namespace App\Http\Controllers\Documentos;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Validator;

use App\Models\Documento;
use App\Models\DocumentoVersion;

trait DocumentosBaseTrait
{
    // Añade un documento nuevo
    protected function __addDocumento(Request $request, $model, $editingId)
    {
        // Action = 'new', 'update', 'version'
        $action = 'new';
        if ($_action = $request->get('_action')) {
            $action = $_action;
        }
        // Guardamos el documento y el fichero
        $res = $this->save_document($request, $action);

        // ERROR de validación de datos
        if ($res['result'] == 'validator') {
            return response()->json($res['validator']->errors(), 422);
        }
        // ERROR: fichero no válido
        if ($res['result'] == 'invalid') {
            return response()->json('¡El fichero que está tratando de subir no es válido!', 422);
        }

        // Relacionamos el nuevo documento con la tabla principal
        $msg = '';
        $documento = $res['result'];
        if ($action == 'version') {
            $msg = 'Nueva versión del documento añadida.';
        } else {
            $instance = $model::find($editingId);
            if ($instance) {
                    if ($action == 'update') {
                        if ($res['pivot_data'] != null) {
                            $instance->documentos()->updateExistingPivot($documento->id, $res['pivot_data']);
                        }
                    } else {
                        $instance->documentos()->save($documento, $res['pivot_data']);
                    }
            } else {
                $msg = '¡No se ha podido ' . ($action == 'update' ? 'actualizar' : 'salvar') . ' el documento!';
                return [
                    'result' => 'error',
                    'msg' => $msg
                ];
            }

            $msg = ($action == 'update') ? 'Datos del documento modificados.' : 'Nuevo documento añadido.';
        }

        return [
            'result' => 'success',
            'msg' => $msg
        ];
    }

    // Devuelve los datos de un documento para editarlo
    // URL: ../documentos/{id}/data
    public function __getDocumentoData(Request $request, $id, $model)
    {
        $table_name = with(new $model)->documentos_pivot_table;

        $documento = Documento::join($table_name, $table_name . '.documento_id', '=', 'documentos.id')
                        ->join('tipos_documentos', 'tipos_documentos.id', '=', 'documentos.tipo_documento_id')
                        ->select([ 'documentos.*', $table_name . '.*', 'tipos_documentos.nombre AS tipo_documento_nombre' ])
                        ->where('documentos.id', '=', $id)
                        ->first();

        $tags = $documento->tagListNormalized;
        $documento['tags'] = serialize($tags);

        return $documento;
    }

    // *************************************************************************
    //  PRIVADOS
    // *************************************************************************

    // Hace toda la gestión de crear, modificar o versionar un documento
    private function save_document(Request $request, $action)
    {
        $fields = $request->all();
        if (isset($fields['nombre_documento'])) {
            $fields['nombre'] = $fields['nombre_documento'];
            unset($fields['nombre_documento']);
        }

        $validator = $this->documents_validator($fields, $action == 'update');
        if ($validator->fails()) {
            return [ 'result' => 'validator', 'validator' => $validator ];
        }

        // Campos especiales para añadir documentos desde el contrato
        unset($fields['tipo_documento_ambito']);
        if (isset($fields['centro_id_documento'])) {
            unset($fields['centro_id_documento']);
        }
        //

        $file = null;
        if ($action != 'update') {
            // New, Version
            $file = $request->file;
            if (!$file->isValid()) {
                return array('result' => 'invalid');
            }
        }

        $documento = null;
        if ($action != 'new') {
            // Update, Version
            $documento = Documento::findOrFail($fields['id']);
            // Nueva versión del documento
            if ($action == 'version') {
                $dv = new DocumentoVersion;
                $dv->id = $documento->id;
                $dv->version = $documento->version;
                $dv->tipo_documento_id = $documento->tipo_documento_id;
                $dv->nombre = $documento->nombre;
                $dv->fecha_documento = $documento->fecha_documento;
                $dv->fecha_caducidad = $documento->fecha_caducidad;
                $dv->notas = $documento->notas;
                $dv->filename = $documento->filename;
                $dv->mime = $documento->mime;
                $dv->original_filename = $documento->original_filename;
                $dv->save();
            }
        }

        if ($action != 'update') {
            // New, Version
            $this->save_file($file, $fields);

            if ($action == 'new') {
                // Saves the document
                $documento = Documento::create($fields);
                $documento->tag(explode(',', $request->tags));
            }
        }

        if ($action != 'new') {
            // Update, Version
            if ($action == 'update') {
                $fields['filename'] = $documento->filename;
            }
            $documento->fill($fields);
            if ($action == 'version') {
                $documento->validacion_id = null;
            }
            $documento->save();
            $documento->retag(explode(',', $request->tags));
        }

        $pivot_data = $this->search_pivot_data($fields);

        return array('result' => $documento, 'pivot_data' => $pivot_data);
    }

    // Save the file
    private function save_file($file, &$fields)
    {
        $filename = $file->getFilename() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('', $filename, 'doc');

        $fields['mime'] = $file->getClientMimeType();
        $fields['original_filename'] = $file->getClientOriginalName();
        $fields['filename'] = $filename;
    }

    // Busca en los datos del request aquellos campos que no sean de la tabla
    // 'documentos' y que por tanto son datos de la tabla pivot
    private function search_pivot_data($f)
    {
        $document_fields = array_flip(with(new Documento)->getFillable());
        $document_fields = array_merge($document_fields, [
            'id' => 0,
            'tags' => 0,
            '_action' => 0,
            'tipo_caducidad' => 0,
            'file' => 0,
        ]);

        $diff = array_diff_key($f, $document_fields);

        return $diff;
    }


    // Validator para el alta y modificación
    private function documents_validator(array $data, $isUpdate = false)
    {
        $rules = [
            'nombre' => 'required|max:100',
            'tipo_documento_id' => 'required|min:1',
            'fecha_documento' => 'required|date_format:d/m/Y',
            'fecha_caducidad' => ($data['tipo_caducidad'] == 'N') ? '' : 'required|date_format:d/m/Y',
            'version' => 'required',
            'file' => $isUpdate ? '' : 'required|file|mimes:' . config('cae.mimes_permitidos'),
        ];
        if (($data['tipo_documento_ambito'] === 'CEN') && isset($data['centro_id_documento'])) {
            $rules['centro_id_documento'] = 'required|min:1';
        }
        $validator = Validator::make($data, $rules);

        $fields_names = [
            'nombre' => 'Nombre',
            'tipo_documento_id' => 'Tipo de documento',
            'fecha_documento' => 'Fecha Documento',
            'fecha_caducidad' => 'Fecha Caducidad',
            'version' => 'Versión',
            'file' => 'Fichero',
            'centro_id_documento' => 'Centro de trabajo'
        ];
        $validator->setAttributeNames($fields_names);

        return $validator;
    }

}
