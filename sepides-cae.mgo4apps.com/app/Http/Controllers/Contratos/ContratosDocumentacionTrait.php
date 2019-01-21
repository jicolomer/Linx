<?php
namespace App\Http\Controllers\Contratos;

use Illuminate\Http\Request;
use Auth;
use Laracasts\Flash\Flash;
use Jenssegers\Date\Date;
use Yajra\Datatables\Datatables;

use App\Models\Contrato;
use App\Models\Documento;
use App\Models\TipoDocumento;
use App\Models\Empresa;
use App\Models\Trabajador;
use App\Models\Maquina;
use App\Models\Centro;
use App\Models\Aviso;

/*
 *  Trait creado sólo para el controller de Contratos.
 *  El propósito es tener menos líneas de código en el controller
 */
trait ContratosDocumentacionTrait
{
    // Adjunta documentos al contrato desde el formulario de selección de
    // documentación
    public function addDocumentacion(Request $request)
    {
        if ($ids = $request->get('ids')) {
            $ids_array = explode(',', $ids);
            $contrato = $this->currentContrato();
            $ambito = $this->getParamFromFilter($request, 'a');
            foreach ($ids_array as $documento_id) {
                switch ($ambito) {
                    case 'EMP':
                        $attr = $this->buscaDocumento(Empresa::findOrFail(0), $documento_id, 'empresa_id');
                        if (count($attr) != 1) {
                            foreach ($contrato->centros()->get() as $centro) {
                                $attr = $this->buscaDocumento($centro, $documento_id, 'centro_id');
                                if (count($attr) == 1) {
                                    $attr['empresa_id'] = 0;
                                    break;
                                }
                            }
                        }
                        break;
                    case 'CTA':
                        $contratista_id = $this->getParamFromFilter($request, 'c');
                        $attr = $this->buscaDocumento(Empresa::findOrFail($contratista_id), $documento_id, 'empresa_id');
                        break;
                    case 'TRA':
                        $trabajador_id = $this->getParamFromFilter($request, 't');
                        $trabajador = Trabajador::findOrFail($trabajador_id);
                        $contratista_id = $trabajador->empresa_id;
                        $attr = $this->buscaDocumento($trabajador, $documento_id, 'trabajador_id');
                        break;
                    case 'MAQ':
                        $maquina_id = $this->getParamFromFilter($request, 'm');
                        $maquina = Maquina::findOrFail($maquina_id);
                        $contratista_id = $maquina->empresa_id;
                        $attr = $this->buscaDocumento($maquina, $documento_id, 'maquina_id');
                        break;
                }
                if ($attr) {
                    $contrato->documentos()->attach($documento_id, $attr);
                }
            }
            // Avisos
            if ($ambito == 'EMP') {
                // Emp. ppal ha añadido documentos
                //      -> avisar a todas las personas de contacto de todos los contratistas del contrato
                Aviso::createAviso(
                    'La empresa principal ha añadido ' .
                        (count($ids_array) > 1 ? 'varios documentos' : 'un documento') .
                        ' al contrato <strong>' . $contrato->nombre .
                        '</strong> <em>(REF. ' . $contrato->referencia . ')</em>.',
                    route('contratos.edit', $contrato->id) . '#t5',
                    $contrato->personas_contacto()->pluck('id')
                );
            } else {
                // Contratista ha añadido documentos CTA o TRA o MAQ
                //      -> avisar a emp. ppal
                //      -> avisar a contratista si lo ha hecho el subcontratista y viceversa
                $avisarA = $contrato->getUsuariosEmpresaPrincipal();
                $contactos_externos = null;
                $subcontratistas = $contrato->subcontratistas($contratista_id);
                if ($subcontratistas->count() == 0) {
                    // El documento es del subcontratista
                    $contratista = $contrato->contratistas(false)->wherePivot('subcontratista_id', $contratista_id)->first();
                    if ($contratista) {
                        $contactos_externos = $contrato->personas_contacto($contratista->id)->pluck('id')->toArray();
                    }
                } else {
                    // El documento es del contratista
                    $contactos_externos = [];
                    $sub_ids = $subcontratistas->pluck('id');
                    foreach ($sub_ids as $key => $id) {
                        $contactos_externos = array_merge($contactos_externos, $contrato->personas_contacto($id)->pluck('id')->toArray());
                    }
                }
                if ($contactos_externos != null) {
                    $avisarA = array_merge($avisarA, $contactos_externos);
                }
                Aviso::createAviso(
                    'La empresa <strong>' . Empresa::getNombreEmpresa($contratista_id) . '</strong> ha añadido ' .
                        (count($ids_array) > 1 ? 'varios documentos' : 'un documento') .
                        ' al contrato <strong>' . $contrato->nombre .
                        '</strong> <em>(REF. ' . $contrato->referencia . ')</em>.',
                    route('contratos.contratista', [$contrato->id, $contratista_id]) . '#t2?r=' . route('contratos.edit', $contrato->id) . '#t6',
                    $avisarA
                );
            }
            return [
                'result' => 'success',
                'msg' => 'El documento se ha añadido al contrato.'
            ];
        }
        return [
            'result' => 'error',
            'msg' => '¡El documento no se ha podido añadir al contrato!'
        ];
    }

    // Busca el documento en el modelo dado y lo guarda en un campo del pivot contratos_doc
    private function buscaDocumento($model, $documento_id, $field_name)
    {
        $res = [];
        $doc = $model->documentos()->where('documento_id', '=', $documento_id);
        if ($doc->count() == 1) {
            $res[$field_name] = $model->id;
        }
        return $res;
    }

    private function getParamFromFilter(Request $request, $param_name)
    {
        if ($f = $request->get('filter')) {
            parse_str($f, $params);
            return $params[$param_name];
        }
        return '';
    }

    // Quita un documento del contrato
    public function detachDocumentacion($id)
    {
        $contrato = $this->currentContrato();
        $contrato->documentos()->detach([$id]);
        return [
            'result' => 'success',
            'msg' => 'Se ha quitado el <strong>documento</strong> de este contrato.'
        ];
    }

    // Devuelve la lista de documentos (datatable) para seleccionar y adjuntar
    public function listaDocumentacion(Request $request)
    {
        $ambito = $request->get('a');
        $tipo_doc = $request->get('td');
        $contratista_id = $request->get('c');
        $trabajador_id = $request->get('t');
        $maquina_id = $request->get('m');

        $contrato = $this->currentContrato();
        $existing_docs = $contrato->documentos()->pluck('id');
        switch ($ambito) {
            case 'EMP':
                $documentos = $this->filtraListaDocumentacion(Empresa::findOrFail(0), $existing_docs, $tipo_doc);
                // Añadimos también lo de Centros
                foreach ($contrato->centros()->get() as $centro) {
                    $doc_centro = $this->filtraListaDocumentacion($centro, $existing_docs, $tipo_doc);
                    $documentos = $documentos->merge($doc_centro);
                }
                break;
            case 'CTA':
                $documentos = $this->filtraListaDocumentacion(Empresa::findOrFail($contratista_id), $existing_docs, $tipo_doc);
                break;
            case 'TRA':
                $documentos = $this->filtraListaDocumentacion(Trabajador::findOrFail($trabajador_id), $existing_docs, $tipo_doc);
                break;
            case 'MAQ':
                $documentos = $this->filtraListaDocumentacion(Maquina::findOrFail($maquina_id), $existing_docs, $tipo_doc);
                break;
            default:
                $documentos = "";
        }
        $datatable = Datatables::of($documentos)
            ->setRowId('id')
            ->editColumn('ambito', function ($documento) {
                $tipo = TipoDocumento::findOrFail($documento->tipo_documento_id);
                return config('enums.doc_scopes')[$tipo->ambito];
            })
            ->addColumn('status_caducidad', function ($documento) {
                return $this->getCaducidadStatusColumn($documento->statusCaducidad());
            })
            ->addColumn('status_validacion', function ($documento) {
                return $this->getValidacionStatusColumn($documento->statusValidacion());
            })
            ->addColumn('tags', function ($documento) {
                return $this->getTagsColumn($documento);
            })
            ->rawColumns(['status_caducidad','status_validacion','tags']);

        return $datatable->make(true);
    }

    private function filtraListaDocumentacion($model, $existing_docs, $tipo_doc)
    {
        $docs = $model->documentos()->where('activo', true);
        // Quitamos los documentos ya adjuntados al contrato
        $docs = $docs->whereNotIn('id', $existing_docs);
        if ($tipo_doc) {
            $docs = $docs->where('tipo_documento_id', '=', $tipo_doc);
        }
        return $docs->get();
    }

    // Datos para las DT de documentación faltante
    public function docFaltanteData(Request $request)
    {
        $contrato = $this->currentContrato();
        $ambito = $request->get('a');
        $contratista_id = $request->get('c');
        $trabajador_id = $request->get('t');
        $maquina_id = $request->get('m');

        if ($ambito == 'CTA' && $contratista_id != null) {
            $tipos_documentos = $this->tiposDocumentosFaltantes($contrato, $ambito, null, $contratista_id)->get();
        } elseif ($ambito == 'TRA' && $trabajador_id != null) {
            $tipos_documentos = $this->tiposDocumentosFaltantes($contrato, $ambito, null, null, $trabajador_id)->get();
        } elseif ($ambito == 'MAQ' && $maquina_id != null) {
            $tipos_documentos = $this->tiposDocumentosFaltantes($contrato, $ambito, null, null, null, $maquina_id)->get();
        } else {
            $tipos_documentos = $this->tiposDocumentosFaltantes($contrato, $ambito)->get();
        }
        if ($ambito === 'EMP') {
            // Añadimos también lo de Centros
            foreach ($contrato->centros()->get() as $centro) {
                $doc_centros = $this->tiposDocumentosFaltantes($contrato, 'CEN', $centro->id)->get();
                $tipos_documentos = $tipos_documentos->merge($doc_centros);
            }
        }
        $datatable = Datatables::of($tipos_documentos)
            ->editColumn('ambito', function ($tipo) {
                return config('enums.doc_scopes')[$tipo->ambito];
            })
            ->editColumn('tipo_caducidad', function ($tipo) {
                return config('enums.tipos_caducidad')[$tipo->tipo_caducidad];
            })
            ->addColumn('tags', function ($documento) {
                return $this->getTagsColumn($documento);
            })
            ->addColumn('obligatorio', function ($tipo_documento) {
                return $this->getCheckColumn($tipo_documento->pivot->obligatorio == 1);
            })
            ->addColumn('actions', function ($tipo_documento) use ($ambito, $contratista_id, $trabajador_id, $maquina_id) {
                $filtro = 'td=' . $tipo_documento->id;
                if ($ambito) {
                    $filtro .= '&a=' . $ambito;
                }
                if ($ambito == 'CTA') {
                    $filtro .= '&c=' . $contratista_id;
                } elseif ($ambito == 'TRA') {
                    $filtro .= '&t=' . $trabajador_id;
                } elseif ($ambito == 'MAQ') {
                    $filtro .= '&m=' . $maquina_id;
                }
                return '<button type="button" class="btn btn-danger bootstrap-modal-form-open" data-toggle="modal" data-target="#adjuntar-doc-modal-dialog" data-filter="' . $filtro .
                            '"><i class="fa fa-level-down"></i> &nbsp;Adjuntar</button>';
            })
            ->rawColumns(['tags','obligatorio','actions']);

        return $datatable->make(true);
    }

    // Devuelve los tipos de documentos que faltan por ámbito
    private function tiposDocumentosFaltantes($contrato, $ambito = null, $centro_id = null, $contratista_id = null, $trabajador_id = null, $maquina_id = null)
    {
        // Documentación requerida del contrato
        $documentacion_requerida = $contrato->tipos_documentos();
        if ($ambito != null) {
            $documentacion_requerida = $documentacion_requerida->where('ambito', '=', $ambito);
        }
        $doc_req = $documentacion_requerida->pluck('id')->toArray();

        // Documentos asociados al contrato
        $documentos = $contrato->documentos()->where('activo', '=', true);
        if ($centro_id != null) {
            $documentos = $documentos->wherePivot('centro_id', '=', $centro_id);
        } elseif ($contratista_id != null) {
            $documentos = $documentos->wherePivot('empresa_id', '=', $contratista_id);
        } elseif ($trabajador_id != null) {
            $documentos = $documentos->wherePivot('trabajador_id', '=', $trabajador_id);
        } elseif ($maquina_id != null) {
            $documentos = $documentos->wherePivot('maquina_id', '=', $maquina_id);
        }
        $doc_ids = $documentos->pluck('tipo_documento_id')->toArray();

        // Los que faltan
        $faltan = array_diff($doc_req, $doc_ids);
        $tipos = $contrato->tipos_documentos()->whereIn('id', $faltan);
        return $tipos;
    }

    // Datos para las DT de documentación del contrato
    public function documentacionData(Request $request)
    {
        $ambito = $request->get('a');
        $noRemove = $request->get('noRemove');
        $contrato = $this->currentContrato();
        $documentos = Documento::join('contratos_doc', 'contratos_doc.documento_id', '=', 'documentos.id')
                               ->join('tipos_documentos', 'tipos_documentos.id', '=', 'documentos.tipo_documento_id')
                               ->select([ 'documentos.*', 'contratos_doc.*', 'tipos_documentos.ambito' ])
                               ->where('contratos_doc.contrato_id', '=', $contrato->id);

        if ($ambito === 'EMP') {
            $documentos = $documentos->where('contratos_doc.empresa_id', '=', 0);
        } elseif ($ambito === 'CTA') {
            if ($contratista_id = $request->get('c')) {
                $documentos = $documentos->where('contratos_doc.empresa_id', '=', $contratista_id);
            }
        } elseif ($ambito === 'TRA') {
            if ($trabajador_id = $request->get('t')) {
                $documentos = $documentos->where('contratos_doc.trabajador_id', '=', $trabajador_id);
            }
        } elseif ($ambito === 'MAQ') {
            if ($maquina_id = $request->get('m')) {
                $documentos = $documentos->where('contratos_doc.maquina_id', '=', $maquina_id);
            }
        }

        $datatable = Datatables::of($documentos)
            ->setRowId('id')
            ->editColumn('ambito', function ($documento) {
                return config('enums.doc_scopes')[$documento->ambito];
            })
            ->addColumn('centro', function ($documento) {
                return $this->getCentroColumn($documento->centro_id);
            })
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
            ->addColumn('actions', function ($documento) use ($noRemove) {
                $html =  '<div class="btn-group">' .
                            '<a href="' . route('getfile', $documento->id) . '" target="_blank" class="btn btn-default btn-sm" data-toggle="tooltip" title="Ver/Descargar"><i class="fa fa-download text-light-blue"></i></a>';
                if ($noRemove != true) {
                    $html .= '<a class="btn btn-default btn-sm" data-action="remove" data-id="'.$documento->id.'" data-toggle="tooltip" title="Quitar del contrato"><i class="fa fa-times text-red"></i></a>';
                }
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
                    $html .= '<a class="btn btn-default btn-sm" data-action="validate" data-id="'.$documento->id.'" data-toggle="tooltip" title="'.$label.'"><i class="fa fa-'.$icon.'"></i></a>';
                }
                return $html . '</div>';
            })
            ->rawColumns(['centro','status_caducidad','status_validacion','tags','actions']);

        return $datatable->make(true);
    }

    // Se elimina un documento requerido del contrato
    public function detachDocumentoRequerido(Request $request, $id)
    {
        // Llamo al método del trait
        $res = $this->detachTipoDocumento($request, $id);
        if ($res['result'] === 'success') {
            // Eliminados los documentos adjuntados al contrato que sean de tipo eliminado
            $contrato = $this->currentContrato();
            $docs_array = $contrato->documentos()->where('tipo_documento_id', '=', $id)->pluck('id')->toArray();
            $contrato->documentos()->detach($docs_array);
        }
        return $res;
    }

    private function currentContrato($id = null)
    {
        if ($id == null) {
            $id = $this->getEditingId();
        }

        $contrato = Contrato::find($id);
        return $contrato;
    }
}
