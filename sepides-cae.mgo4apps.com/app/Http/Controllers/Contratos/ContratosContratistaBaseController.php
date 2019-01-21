<?php
namespace App\Http\Controllers\Contratos;

use Illuminate\Http\Request;
use Auth;
use Session;
use URL;
use Validator;

use Jenssegers\Date\Date;
use Yajra\Datatables\Datatables;

use App\Models\Contrato;
use App\Models\ContratoMaquina;
use App\Models\ContratoTrabajador;
use App\Models\Empresa;
use App\Models\Trabajador;
use App\Models\Maquina;
use App\Models\Aviso;
use App\User;

class ContratosContratistaBaseController extends \App\Http\Controllers\Base\BaseController
{
    use ContratosDocumentacionTrait;

    const CURRENT_URL_KEY = 'contratos_contratistas_current_url';
    const EDITANDO_CONTRATISTA_KEY = 'contratos_contratistas_editando_contratista_id';
    const EDITANDO_SUBCONTRATISTA_KEY = 'contratos_contratistas_editando_subcontratista_id';
    const EDITANDO_TRABAJADOR_KEY = 'contratos_contratistas_editando_trabajador_id';
    const EDITANDO_MAQUINA_KEY = 'contratos_contratistas_editando_maquina_id';

    public function __construct()
    {
        parent::__construct(Contrato::class, 'contratos');
    }


    // *************************************************************************
    //  TRABAJADORES
    // *************************************************************************
    // Para la lista del DT de los trabajadores del contrato
    public function trabajadoresData(Request $request)
    {
        $contrato = $this->currentContrato();
        $empresa_id = $this->getEmpresaIdEditando();

        $trabajadores = Trabajador::leftJoin('contratos_trabajadores', 'trabajadores.id', '=', 'contratos_trabajadores.trabajador_id')
                                  ->select(['trabajadores.id', 'trabajadores.nombre', 'trabajadores.apellidos',
                                            'contratos_trabajadores.contrato_id', 'contratos_trabajadores.centro_id', 'contratos_trabajadores.trabajador_id',
                                            'contratos_trabajadores.fecha_inicio_trabajos', 'contratos_trabajadores.fecha_fin_trabajos',
                                            'contratos_trabajadores.trabaja_lunes', 'contratos_trabajadores.trabaja_martes', 'contratos_trabajadores.trabaja_miercoles',
                                            'contratos_trabajadores.trabaja_jueves', 'contratos_trabajadores.trabaja_viernes', 'contratos_trabajadores.trabaja_sabado',
                                            'contratos_trabajadores.trabaja_domingo', 'contratos_trabajadores.permiso_status', 'contratos_trabajadores.permiso_motivo_rechazo',
                                            'contratos_trabajadores.permiso_user_id', 'contratos_trabajadores.permiso_fecha'])
                                  ->where('contratos_trabajadores.contrato_id', '=', $contrato->id)
                                  ->where('trabajadores.empresa_id', '=', $empresa_id)
                                  ->orderBy('contratos_trabajadores.centro_id')
                                  ->orderBy('contratos_trabajadores.fecha_inicio_trabajos')
                                  ->orderBy('contratos_trabajadores.trabajador_id');
        $docButton = true;
        if ($trabajador_id = $request->get('t')) {
            $trabajadores = $trabajadores->where('contratos_trabajadores.trabajador_id', '=', $trabajador_id);
            $docButton = false;
        }

        $datatable = $this->getDefaultDatatable($empresa_id, $trabajadores, 'trabajadores.edit', 'contratos.detachTrabajador', 'contratos.trabajador', 'user', 'l trabajador', $docButton);
        return $datatable
            ->addColumn('trabajador', function ($trabajador) {
                return $trabajador->nombreCompleto() . ' (#' . $trabajador->id . ')';
            })
            ->addColumn('status_doc', function ($trabajador) use ($contrato) {
                return $this->getDocStatusColumn($contrato->statusDocTrabajador($trabajador->id));
            })
            ->make(true);
    }

    // Quitar trabajadores del contrato (y su documentacion)
    public function detachTrabajador($trabajador_id, $centro_id = null, $fecha_inicio_trabajos = null)
    {
        return $this->detachTrabajadorMaquina(
            ContratoTrabajador::class,
            'trabajador_id',
            $trabajador_id,
            $centro_id,
            $fecha_inicio_trabajos,
            'trabajador',
            'el',
            'del',
            'ningún'
        );
    }


    // *************************************************************************
    //  MÁQUINAS
    // *************************************************************************
    // Para la lista del DT de las máquinas del contrato
    public function maquinasData(Request $request)
    {
        $contrato = $this->currentContrato();
        $empresa_id = $this->getEmpresaIdEditando();
        $maquinas = Maquina::leftJoin('contratos_maquinas', 'maquinas.id', '=', 'contratos_maquinas.maquina_id')
                                  ->select(['maquinas.id', 'maquinas.nombre', 'maquinas.tipo_maquina_id',
                                            'contratos_maquinas.contrato_id', 'contratos_maquinas.centro_id', 'contratos_maquinas.maquina_id',
                                            'contratos_maquinas.fecha_inicio_trabajos', 'contratos_maquinas.fecha_fin_trabajos',
                                            'contratos_maquinas.trabaja_lunes', 'contratos_maquinas.trabaja_martes', 'contratos_maquinas.trabaja_miercoles',
                                            'contratos_maquinas.trabaja_jueves', 'contratos_maquinas.trabaja_viernes', 'contratos_maquinas.trabaja_sabado',
                                            'contratos_maquinas.trabaja_domingo', 'contratos_maquinas.permiso_status', 'contratos_maquinas.permiso_motivo_rechazo',
                                            'contratos_maquinas.permiso_user_id', 'contratos_maquinas.permiso_fecha'])
                                  ->where('contratos_maquinas.contrato_id', '=', $contrato->id)
                                  ->where('maquinas.empresa_id', '=', $empresa_id)
                                  ->orderBy('contratos_maquinas.centro_id')
                                  ->orderBy('contratos_maquinas.fecha_inicio_trabajos')
                                  ->orderBy('contratos_maquinas.maquina_id');
        $docButton = true;
        if ($maquina_id = $request->get('m')) {
            $maquinas = $maquinas->where('contratos_maquinas.maquina_id', '=', $maquina_id);
            $docButton = false;
        }

        $datatable = $this->getDefaultDatatable($empresa_id, $maquinas, 'maquinas.edit', 'contratos.detachMaquina', 'contratos.maquina', 'bus', ' la máquina', $docButton);
        return $datatable
            ->addColumn('maquina', function ($maquina) {
                return $maquina->nombre . ' (#' . $maquina->id . ')';
            })
            ->addColumn('status_doc', function ($maquina) use ($contrato) {
                return $this->getDocStatusColumn($contrato->statusDocMaquina($maquina->id));
            })
            ->make(true);
    }

    // Quitar máquinas del contrato (y su documentacion)
    public function detachMaquina($maquina_id, $centro_id = null, $fecha_inicio_trabajos = null)
    {
        return $this->detachTrabajadorMaquina(
            ContratoMaquina::class,
            'maquina_id',
            $maquina_id,
            $centro_id,
            $fecha_inicio_trabajos,
            'máquina',
            'la',
            'de la',
            'ninguna'
        );
    }


    // *************************************************************************
    //  ASOCIAR trajajadores/máquinas
    // *************************************************************************
    // Asignar trabajadores o máquinas al contrato
    public function addTrabajadoresMaquinas(Request $request)
    {
        $fields = $request->only(
            'trabajadores_ids',
            'maquinas_ids',
            'centros_ids',
            'fecha_inicio',
            'fecha_final',
            'lunes',
            'martes',
            'miercoles',
            'jueves',
            'viernes',
            'sabado',
            'domingo'
        );
        $isTrabajadores = array_key_exists('trabajadores_ids', $fields) && ($fields['trabajadores_ids'] != null);
        // Validación
        $validator = $this->validatorForAddTrabajadoresMaquinas($fields, $isTrabajadores);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Preparamos datos
        $contrato = $this->currentContrato();
        $centros = explode(',', $fields['centros_ids']);
        $items = explode(',', $fields[$isTrabajadores ? 'trabajadores_ids' : 'maquinas_ids']);
        foreach ($items as $item_id) {
            foreach ($centros as $centro_id) {
                // Miramos si ya está asignado en esa fecha de inicio
                if ($isTrabajadores) {
                    $existe = ContratoTrabajador::where('trabajador_id', $item_id);
                } else {
                    $existe = ContratoMaquina::where('maquina_id', $item_id);
                }
                $existe = $existe->where('contrato_id', $contrato->id)
                                 ->where('centro_id', $centro_id)
                                 ->whereDate('fecha_inicio_trabajos', Date::createFromFormat('d/m/Y', $fields['fecha_inicio'])->toDateString());
                if ($existe->count() > 0) {
                    return response()->json([
                        'errors' => [
                            'existe' => 'Ya está asignad' . ($isTrabajadores ? 'o el trabajador' : 'a la máquina') . ' #' . $item_id .
                                        ' al centro #' . $centro_id . ' para comenzar los trabajos el ' .
                                        $fields['fecha_inicio']
                        ]
                    ], 422);
                }
                if ($isTrabajadores) {
                    $ctm = new ContratoTrabajador();
                    $ctm->trabajador_id = $item_id;
                } else {
                    $ctm = new ContratoMaquina();
                    $ctm->maquina_id = $item_id;
                }
                $ctm->contrato_id = $contrato->id;
                $ctm->centro_id = $centro_id;
                $ctm->fecha_inicio_trabajos = $fields['fecha_inicio'];
                $ctm->fecha_fin_trabajos = $fields['fecha_final'];
                $ctm->trabaja_lunes = array_key_exists('lunes', $fields) && $fields['lunes'] == 'true';
                $ctm->trabaja_martes = array_key_exists('martes', $fields) && $fields['martes'] == 'true';
                $ctm->trabaja_miercoles = array_key_exists('miercoles', $fields) && $fields['miercoles'] == 'true';
                $ctm->trabaja_jueves = array_key_exists('jueves', $fields) && $fields['jueves'] == 'true';
                $ctm->trabaja_viernes = array_key_exists('viernes', $fields) && $fields['viernes'] == 'true';
                $ctm->trabaja_sabado = array_key_exists('sabado', $fields) && $fields['sabado'] == 'true';
                $ctm->trabaja_domingo = array_key_exists('domingo', $fields) && $fields['domingo'] == 'true';
                $ctm->save();
            }
            // Documentos
            if ($isTrabajadores) {
                $contrato->addDocumentosRequeridos('TRA', Trabajador::findOrFail($item_id), 'trabajador_id');
            } else {
                $contrato->addDocumentosRequeridos('MAQ', Maquina::findOrFail($item_id), 'maquina_id');
            }
        }
        // Aviso
        $contratista_id = Session::get(self::EDITANDO_CONTRATISTA_KEY);
        $subcontratista_id = Session::get(self::EDITANDO_SUBCONTRATISTA_KEY);
        if ($subcontratista_id == false) {
            $empresa_id = $contratista_id;
            $url = route('contratos.contratista', [$contrato->id, $contratista_id]);
        } else {
            $empresa_id = $subcontratista_id;
            $url = route('contratos.subcontratista', [$contrato->id, $contratista_id, $subcontratista_id]);
        }
        if ($isTrabajadores) {
            $itemName = count($items) > 1 ? 'trabajadores' : 'un trabajador';
            $itemName2 = count($items) > 1 ? 'han asignado nuevos trabajadores' : 'ha asignado un nuevo trabajador';
        } else {
            $itemName = count($items) > 1 ? 'máquinas' : 'una máquina';
            $itemName2 = count($items) > 1 ? 'han asignado nuevas máquinas' : 'ha asignado una nueva máquina';
        }
        Aviso::createAviso(
            'La empresa <strong>' . Empresa::getNombreEmpresa($empresa_id) .
            '</strong> ha asignado ' . $itemName .
            ' al contrato ' . $contrato->getNombreAvisos() . '.',
            $url,
            $contrato->getUsuariosEmpresaPrincipal()
        );
        return [
            'result' => 'success',
            'msg' => 'Se ' . $itemName2 . ' al contrato.',
        ];
    }


    // *************************************************************************
    //  PERMISOS ACCESO trabajadores y máquinas
    // *************************************************************************
    public function updateAcceso(Request $request)
    {
        if ($boton = $request->get('boton')) {
            if ($request->has('tipo')) {
                $tipo = $request->tipo;
                $item_id = $request->get('item_id');
                $centro_id = $request->get('centro_id');
                $fecha_inicio = $request->get('fecha_inicio');
                if ($fecha_inicio != null) {
                    $fecha_inicio = Date::createFromFormat('Ymd', $fecha_inicio)->toDateString();
                }
                $motivo_rechazo = $request->get('motivo');

                $contrato_id = $this->getEditingId();
                $contrato = $this->currentContrato($contrato_id);
                $empresa_id = $this->getEmpresaIdEditando();

                $itemClass = ($tipo === "1") ? ContratoMaquina::class : ContratoTrabajador::class;
                $itemFieldName = ($tipo === "1") ? 'maquina_id' : 'trabajador_id';
                $allowAccess = true;
                $verbo = 'concedido';

                // Trabajador o máquina INDIVIDUAL
                if ($boton == 'lock' || $boton == 'unlock') {
                    $items = $itemClass::where('contrato_id', '=', $contrato_id)
                                       ->where('centro_id', '=', $centro_id)
                                       ->where($itemFieldName, '=', $item_id)
                                       ->whereDate('fecha_inicio_trabajos', '=', $fecha_inicio);
                    $sufijo = (($tipo === "1") ? ' la máquina #' : 'l trabajador #') . $item_id;
                }
                // Todos trabajadores o máquinas
                if ($boton == 'lockAll' || $boton == 'unlockAll') {
                    $items = $itemClass::where('contrato_id', '=', $contrato_id);
                    $sufijo = ($tipo === "1") ? ' todas las máquinas' : ' todos los trabajadores';
                }
                // PERMISO a los OK
                if ($boton == 'unlockOK') {
                    $items_ok_ids = [];
                    $items_ids = $itemClass::having('contrato_id', '=', $contrato_id)
                                       ->groupBy('contrato_id', $itemFieldName)
                                       ->pluck($itemFieldName);
                    foreach ($items_ids->toArray() as $item_id) {
                        if ($tipo === "1") {
                            $docStatus = $contrato->statusDocMaquina($item_id);
                        } else {
                            $docStatus = $contrato->statusDocTrabajador($item_id);
                        }
                        if ($docStatus === 0) {
                            $items_ok_ids[] = $item_id;
                        }
                    }
                    $items = $itemClass::where('contrato_id', '=', $contrato_id)
                                       ->whereIn($itemFieldName, $items_ok_ids);
                    $sufijo = (($tipo === "1") ? ' las máquinas' : ' los trabajadores') . ' con documentación correcta';
                }
                // Denegar
                if ($boton == 'lock' || $boton == 'lockAll') {
                    $allowAccess = false;
                    $verbo = 'denegado';
                }

                // Cambiamos los permisos correspondientes
                foreach ($items->get() as $item) {
                    $item['permiso_status'] = $allowAccess;
                    $item['permiso_user_id'] = Auth::user()->id;
                    $item['permiso_fecha'] = Date::now();
                    if ($motivo_rechazo != null) {
                        $item['permiso_motivo_rechazo'] = $motivo_rechazo;
                    }
                    $item->save();
                }

                // AVISO - A la empresa del trabajador o máquina
                $msg = 'Se ha <strong>' . $verbo . '</strong> permiso a' . $sufijo;
                Aviso::createAviso(
                    $msg . ' del contrato ' . $contrato->getNombreAvisos() . '.',
                    $this->getCurrentUrl() . '#t' . ($tipo === "1" ? '4' : '3'),
                    $contrato->personas_contacto($empresa_id)->pluck('id')->toArray()
                );

                return [
                    'result' => 'success',
                    'msg' =>  $msg . '.',
                ];
            }
        }

        return [
            'result' => 'error',
            'msg' => 'No se ha podido modificar el permiso de acceso (Datos incorrectos).'
        ];
    }


    // *************************************************************************
    //  HELPERS y MÉTODOS COMUNES
    // *************************************************************************
    // Validación para añadir trabajadores/maquinas
    private function validatorForAddTrabajadoresMaquinas(array $data, $isTrabajadores)
    {
        $validator = null;
        $rules = [
            'centros_ids' => 'required|min:1',
            'fecha_inicio' => 'required|date_format:d/m/Y|before:fecha_final',
            'fecha_final' => 'required|date_format:d/m/Y|after:fecha_inicio',
            'lunes' => 'required_without_all:martes,miercoles,jueves,viernes,sabado,domingo',
            'martes' => 'required_without_all:lunes,miercoles,jueves,viernes,sabado,domingo',
            'miercoles' => 'required_without_all:lunes,martes,jueves,viernes,sabado,domingo',
            'jueves' => 'required_without_all:lunes,martes,miercoles,viernes,sabado,domingo',
            'viernes' => 'required_without_all:lunes,martes,miercoles,jueves,sabado,domingo',
            'sabado' => 'required_without_all:lunes,martes,miercoles,jueves,viernes,domingo',
            'domingo' => 'required_without_all:lunes,martes,miercoles,jueves,viernes,sabado',
        ];
        $rules[($isTrabajadores ? 'trabajadores' : 'maquinas') . '_ids'] = 'required|min:1';
        $fields_names = [
            'centros_ids' => 'lista de Centros',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_final' => 'Fecha Final',
        ];
        $fields_names[($isTrabajadores ? 'trabajadores' : 'maquinas') . '_ids'] = 'lista de ' . ($isTrabajadores ? 'Trabajadores' : 'Máquinas');
        $validator = Validator::make($data, $rules);
        $validator->setAttributeNames($fields_names);
        return $validator;
    }

    // Default Datatable para trabajadores/máquinas asociados al contrato
    private function getDefaultDatatable($empresa_id, $data, $editRouteName, $detachRouteName, $itemEditRouteName, $editIcon, $editText, $docButton = true)
    {
        $datatable = Datatables::of($data)
            ->addColumn('centro', function ($item) {
                return $this->getCentroColumn($item->centro_id);
            })
            ->addColumn('fecha_inicio', function ($item) {
                return Date::parse($item->fecha_inicio_trabajos)->format('d/m/Y');
            })
            ->addColumn('fecha_fin', function ($item) {
                return Date::parse($item->fecha_fin_trabajos)->format('d/m/Y');
            })
            ->addColumn('dias_trabajo', function ($item) {
                $t = '';
                if ($item->trabaja_lunes) {
                    $t .= 'Lu, ';
                }
                if ($item->trabaja_martes) {
                    $t .= 'Ma, ';
                }
                if ($item->trabaja_miercoles) {
                    $t .= 'Mi, ';
                }
                if ($item->trabaja_jueves) {
                    $t .= 'Ju, ';
                }
                if ($item->trabaja_viernes) {
                    $t .= 'Vi, ';
                }
                if ($item->trabaja_sabado) {
                    $t .= 'Sa, ';
                }
                if ($item->trabaja_domingo) {
                    $t .= 'Do, ';
                }
                $t = rtrim($t, ', ');
                if ($t === 'Lu, Ma, Mi, Ju, Vi, Sa, Do') {
                    return 'Todos';
                } elseif ($t === 'Lu, Ma, Mi, Ju, Vi') {
                    return 'Entre semana';
                } elseif ($t === 'Sa, Do') {
                    return 'Fines de semana';
                } else {
                    return $t;
                }
            })
            ->addColumn('status_permiso', function ($item) {
                if ($item->permiso_status === null) {
                    $tt = 'No evaluado';
                    $attr = 'ban text-dark-gray';
                } else {
                    if ($item->permiso_status) {
                        $tt = '<strong>Permiso concedido</strong>';
                    } else {
                        $tt ='<strong>¡Permiso DENEGADO!</strong><br />Motivo: <em>' . $item->permiso_motivo_rechazo . '</em>';
                    }
                    $user = User::find($item->permiso_user_id);
                    if ($user != null) {
                        $tt .= '<br />Responsable: <em>' . $user->nombre . '</em>';
                    }
                    $tt .= '<br />Fecha: <em>' . Date::parse($item->permiso_fecha)->format('d/m/Y') . '</em>';
                    $attr = $item->permiso_status ? 'thumbs-up text-green' : 'thumbs-down text-danger';
                }
                return '<span data-toggle="tooltip" data-placement="left" data-html="true" title="' . $tt .
                       '"><i class="fa fa-lg fa-' . $attr . '"></i></span>';
            })
            ->addColumn('actions', function ($item) use ($empresa_id, $editRouteName, $detachRouteName, $itemEditRouteName, $editIcon, $editText, $docButton) {
                $html = '<div class="btn-group">';
                if ($docButton) {
                    $html .= '<a href="' . route($itemEditRouteName, [$item->contrato_id, $item->id]) . '?r=' . $this->getCurrentUrl() . '%23t3" class="btn btn-default btn-sm" data-toggle="tooltip" title="Documentación"><i class="fa fa-files-o"></i></a>';
                }
                $html .= '<a href="' . route($editRouteName, $item->id) . '?r=' . $this->getCurrentUrl() . '%23t3" class="btn btn-default btn-sm" data-toggle="tooltip" title="Datos de' .
                                $editText . '"><i class="fa fa-' . $editIcon . ' text-blue"></i></a>';
                if (Auth::user()->empresa_id == $empresa_id) {
                    $html .= '<a href="' . route($detachRouteName, [$item->id, $item->centro_id, Date::parse($item->fecha_inicio_trabajos)->format('Ymd')]) . '" class="btn btn-default btn-sm detach-button" data-toggle="tooltip" title="Quitar del contrato"><i class="fa fa-times text-red"></i></a>';
                }
                // Botones Permisos acceso
                if (Auth::user()->can('acceso.update')) {
                    // Trabajador o Máquina ?
                    $tipo =  strpos(get_class($item), 'Trabajador') != false ? 0 : 1;
                    $fecha = Date::parse($item->fecha_inicio_trabajos)->format('Ymd');
                    // Botón permitir acceso
                    if ($item->permiso_status !== 1) {
                        $html .= '<button data-tipo="' . $tipo . '" data-id="' . $item->id . '" data-centro="' . $item->centro_id . '" data-fecha="' . $fecha .
                                    '" class="unlockBtn btn btn-success btn-sm" data-toggle="tooltip" title="Dar permiso de acceso"><i class="fa fa-unlock"></i></a>';
                    }
                    // Botón bloquear acceso
                    if ($item->permiso_status !== 0) {
                        $html .= '<button data-tipo="' . $tipo . '" data-id="' . $item->id . '" data-centro="' . $item->centro_id . '" data-fecha="' . $fecha .
                                    '" class="lockBtn btn btn-danger btn-sm" data-toggle="tooltip" title="Denegar permiso de acceso"><i class="fa fa-lock"></i></a>';
                    }
                }
                return $html . '</div>';
            })
            ->rawColumns(['centro','status_permiso','actions','status_doc']);

        return $datatable;
    }

    // Método que quita un trabajador/máquina del contrato (y su documentación)
    private function detachTrabajadorMaquina($model, $pivotField, $id, $centro_id, $fecha_inicio_trabajos, $singleName, $pronoum, $de, $ningun)
    {
        $contrato = $this->currentContrato();
        $query = $model::where('contrato_id', '=', $contrato->id)
                       ->where($pivotField, '=', $id);
        if ($centro_id != null) {
            $query = $query->where('centro_id', '=', $centro_id);
        }
        if ($fecha_inicio_trabajos != null) {
            $query = $query->whereDate('fecha_inicio_trabajos', '=', $fecha_inicio_trabajos);
        }
        $res = $query->delete();

        if ($res > 0) {
            $msg = "Se ha quitado $pronoum $singleName del contrato.";
            // Si ya no hay más vínculos del trabajador/máquina en el contrato, eliminamos su documentación
            $count = $model::where('contrato_id', '=', $contrato->id)
                           ->where($pivotField, '=', $id)
                           ->count();
            $msg2 = "<br />No se ha quitado ningún documento $de $singleName.";
            if ($count == 0) {
                $res = $contrato->documentos()->wherePivot($pivotField, '=', $id)->detach();
                if ($res > 0) {
                    $msg2 = "<br />También se ha quitado $res documento $de $singleName.";
                }
            }
            return [
                'result' => 'success',
                'msg' => $msg . $msg2,
            ];
        } else {
            return [
                'result' => 'error',
                'msg' => "¡No se ha podido quitar $ningun $singleName del contrato!"
            ];
        }
    }

    protected function getEmpresaIdEditando()
    {
        $contratista_id = Session::get(self::EDITANDO_CONTRATISTA_KEY);
        $subcontratista_id = Session::get(self::EDITANDO_SUBCONTRATISTA_KEY);
        return ($subcontratista_id === false) ? $contratista_id : $subcontratista_id;
    }

    protected function getCurrentUrl()
    {
        return Session::get(self::CURRENT_URL_KEY);
    }
}
