<?php
namespace App\Http\Controllers\Contratos;

use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;

use Laracasts\Flash\Flash;
use Jenssegers\Date\Date;
use Yajra\Datatables\Datatables;

use App\Models\Contrato;
use App\Models\Empresa;
use App\Models\Trabajador;
use App\Models\Aviso;
use App\User;

/*
 *  Trait creado sólo para el controller de Contratos.
 *  El propósito es tener menos líneas de código en el controller
 */
trait ContratosContratistasTrait
{
    public function contratistasData(Request $request)
    {
        $contratista_id = $request->get('c');
        $contrato = $this->currentContrato();

        if ($contratista_id) {
            $contratistas = $contrato->subcontratistas($contratista_id);
        } else {
            $contratistas = $contrato->contratistas()->wherePivot('subcontratista_id', '=', 0);
            if (Auth::user()->isExterno()) {
                $empresa_id = Auth::user()->empresa_id;
                // Aparece como subcontratista
                if ($contrato->subcontratistas()->where('id', $empresa_id)->count() > 0) {
                    $contratistas = $contrato->contratistas_subcontratista($empresa_id);
                } else {
                    // Es un subcontratista
                    $contratistas = $contratistas->where('id', '=', $empresa_id);
                }
            }
        }

        $datatable = Datatables::of($contratistas)
            ->addColumn('contacto', function ($contratista) use ($contrato) {
                $contacto = $contrato->personas_contacto($contratista->id)->first();
                if ($contacto) {
                    $html = $contacto->nombre;
                    if ($contacto->cargo) {
                        $html .= ' (' . $contacto->cargo . ')';
                    }
                    $data = '';
                    if ($contacto->telefono) {
                        $data .= 'Teléfono: <strong>' . $contacto->telefono . '</strong><br />';
                    }
                    $data .= 'Email: <strong>' . $contacto->email . '</strong><br />';
                    if ($contratista->telefono) {
                        $data .= 'Teléfono empresa: <strong>' . $contratista->telefono . '</strong><br />';
                    }
                    $html .= '&nbsp;&nbsp;<i class="fa fa-plus-circle text-dark-gray" data-toggle="tooltip" data-placement="right" data-html="true" data-title="' .
                        $data . '"></i>';

                    return $html;
                } else {
                    return $contratista->telefono;
                }
            })
            ->addColumn('status_doc', function ($contratista) use ($contrato) {
                return $this->getDocStatusColumn($contrato->statusDocContratista($contratista->id, false));
            })
            ->addColumn('status_trabajadores', function ($contratista) use ($contrato) {
                return $this->getDocStatusColumn($contrato->statusDocTrabajadoresContratista($contratista->id, false));
            })
            ->addColumn('status_maquinas', function ($contratista) use ($contrato) {
                return $this->getDocStatusColumn($contrato->statusDocMaquinasContratista($contratista->id, false));
            })
            ->addColumn('actions', function ($contratista) use ($contratista_id) {
                $html = '<div class="btn-group">' .
                            '<a class="btn btn-default btn-sm" data-id="' . $contratista->id .
                                '"' . (($contratista_id != null) ? ' data-contratista="' . $contratista_id . '"' : '') .
                                ' data-toggle="tooltip" title="Quitar del contrato"><i class="fa fa-times text-red"></i></a>';
                // if (Auth::user()->can('acceso.update')) {
                //     $html .= '<a class="btn btn-default btn-sm" data-id="' . $contratista->id .
                //         '"' . (($contratista_id != null) ? ' data-contratista="' . $contratista_id . '"' : '') .
                //         '><i class="fa fa-lock text-red"></i></a>';
                // }
                return $html . '</div>';
            })
            ->rawColumns(['contacto','status_doc','status_trabajadores','status_maquinas','actions'])
            ->make(true);

        return $datatable;
    }

    public function addContratista(Request $request)
    {
        $fields = $request->all();
        $crear_nuevo = array_key_exists('contratista_nuevo', $fields);
        $contratista_id = $request->get('contratista_id');
        $isSubcontratista = ($contratista_id != null);
        $subcontratista_id = null;
        $validaEmpresa = true;
        $validaPersonaContacto = true;
        $crearNuevoContacto = false;

        // Miramos si ya existe ese subcontratista en la base de datos
        if (($crear_nuevo == true) && ($isSubcontratista == true)) {
            $subcontratista = Empresa::where('cif', '=', $fields['cif'])->first();
            if ($subcontratista != null) {
                $subcontratista_id = $subcontratista->id;
                $validaEmpresa = false;
            }
            // Buscamos a la persona de contacto por su email
            if (isset($fields['email'])) {
                $contacto = Trabajador::where('email', '=', $fields['email'])->first();
                if ($contacto != null) {
                    // Existe -> hay que usar el existente
                    $fields['persona_contacto'] = $contacto->id;
                    $validaPersonaContacto = false;
                } elseif ($subcontratista != null) {
                    // Crear nueva persona de contacto
                    $crearNuevoContacto = true;
                }
            }
        }
        // Si ya existe el subcontratista no hay que validar el cif y la razón social
        // ya que lo vamos a coger directamente de la base de datos.
        $validator = $this->validatorForAddContratista($fields, $validaEmpresa, $validaPersonaContacto);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $contrato = $this->currentContrato();
        if (($crear_nuevo == true) && ($subcontratista_id == null)) {
            // NUEVO
            // 1 - Empresa
            $empresa = Empresa::create([
                'razon_social' => $fields['razon_social'],
                'cif' => $fields['cif'],
            ]);
            // 2 - Adjuntarla al contrato
            if ($isSubcontratista == true) {
                $contrato->contratistas()->attach($contratista_id, ['subcontratista_id' => $empresa->id]);
                Empresa::addSubcontratista($contratista_id, $empresa->id);
                // Aviso al personal de la emp. ppal.
                $this->sendContratistaAviso(Empresa::getNombreEmpresa($contratista_id), $empresa->displayName(), $contrato, true);
            } else {
                $contrato->contratistas()->attach($empresa->id);
                // Aviso al personal de la emp. ppal.
                $this->sendContratistaAviso(Empresa::getNombreEmpresa($empresa->id), null, $contrato, true);
            }
            // 3 - Persona de contacto
            if (($isSubcontratista == false) || (($isSubcontratista == true) && (config('cae.invitar_subcontratistas') == true))) {
                // Creamos la persona de contacto
                $this->createPersonaContacto($fields, $empresa->id, $contrato);
            }
            $msg_ok = 'Se ha creado un <strong>nuevo ' . (($isSubcontratista == true) ? 'sub' : '') . 'contratista</strong> y se ha añadido al contrato.';
        } else {
            // ASOCIAR CONTRATISTA/Subcontratista EXISTENTE
            $msg_documentos = ' y se han <strong>añadido sus documentos</strong> requeridos';
            if ($isSubcontratista == false) {
                // Contratista
                $contratista_id = $fields['contratista_seleccionado'];
                $contrato->contratistas()->attach($contratista_id);
                // Aviso al personal de la emp. ppal.
                $this->sendContratistaAviso(Empresa::getNombreEmpresa($contratista_id), null, $contrato);
                // Adjunta documentos del contratista al contrato
                if ($contrato->tipo_contrato_id != null) {
                    $contrato->addDocumentosRequeridos('CTA', Empresa::find($contratista_id), 'empresa_id');
                } else {
                    $msg_documentos = '';
                }
            } else {
                // Subcontratista
                if ($subcontratista_id == null) {
                    $subcontratista_id = $fields['contratista_seleccionado'];
                }
                $contrato->contratistas()->attach($contratista_id, ['subcontratista_id' => $subcontratista_id]);
                Empresa::addSubcontratista($contratista_id, $subcontratista_id);
                // Aviso al personal de la emp. ppal.
                $this->sendContratistaAviso(Empresa::getNombreEmpresa($contratista_id), Empresa::getNombreEmpresa($subcontratista_id), $contrato);
                // Adjunta documentos del SUBcontratista al contrato
                $contrato->addDocumentosRequeridos('CTA', Empresa::find($subcontratista_id), 'empresa_id');
            }
            if (($crearNuevoContacto == true) && (config('cae.invitar_subcontratistas') == true)) {
                // Crear nuevo contacto para un subcontratista ya existente
                $this->createPersonaContacto($fields, $subcontratista_id, $contrato);
            } elseif (($isSubcontratista == false) || (($isSubcontratista == true) && (config('cae.invitar_subcontratistas') == true))) {
                $trabajador = Trabajador::find($fields['persona_contacto']);
                if ($trabajador != null) {
                    // Guardamos el trabajador como persona de contacto del contratista
                    $contrato->addPersonaContacto(($isSubcontratista == true) ? $subcontratista_id : $contratista_id, $trabajador->id);
                    // Manda el email a la persona de contacto
                    Aviso::createAviso(
                        'Has sido invitado a participar en el contrato ' . $contrato->getNombreAvisos() . '.',
                        route('contratos.edit', $contrato->id),
                        [$trabajador->user_id]
                    );
                    $this->sendInvitacionContratistaContratoEmail(false, $trabajador, $contrato);
                }
            }
            $msg_ok = 'Se ha <strong>asociado el ' . (($isSubcontratista == true) ? 'sub' : '') . 'contratista</strong> al contrato' . $msg_documentos . '.';
        }

        Flash::success($msg_ok);
        return response()->json(['result' => 'success']);
    }

    private function validatorForAddContratista(array $data, $validaEmpresa, $validaPersonaContacto)
    {
        $validator = null;
        if (array_key_exists('contratista_nuevo', $data)) {
            $rules = [];
            $fields_names = [];
            if ($validaEmpresa == true) {
                $rules = [
                    'razon_social' => 'required|max:255|unique:empresas',
                    'cif' => 'required|max:9|unique:empresas',
                ];
                $fields_names = [
                    'razon_social' => 'Razón Social',
                    'cif' => 'CIF/DNI',
                ];
            }
            if ($validaPersonaContacto == true) {
                $rules = array_merge($rules, [
                    'nombre' => 'required|max:50',
                    'apellidos' => 'required|max:100',
                    'nif' => 'required|min:9|max:9|unique:trabajadores',
                    'puesto' => 'required|max:50',
                    'email' => 'required|email|unique:users',
                ]);
                $fields_names = array_merge($fields_names, [
                    'nombre' => 'Nombre',
                    'apellidos' => 'Apellidos',
                    'nif' => 'NIF/DNI',
                    'puesto' => 'Puesto',
                    'email' => 'Email',
                ]);
            }
            $validator = Validator::make($data, $rules);
        } else {
            $rules = ['contratista_seleccionado' => 'required|min:1'];
            $fields_names = [ 'contratista_seleccionado' => 'Contratista' ];
            if ($validaPersonaContacto == true) {
                $rules['persona_contacto'] = 'required|min:1';
                $fields_names['persona_contacto'] = 'Persona de Contacto';
            }
            $validator = Validator::make($data, $rules);
        }
        $validator->setAttributeNames($fields_names);
        return $validator;
    }

    private function createPersonaContacto($fields, $empresa_id, $contrato)
    {
        // 3 - Trabajador
        $trabajador = Trabajador::create([
            'nombre' => $fields['nombre'],
            'apellidos' => $fields['apellidos'],
            'nif' => $fields['nif'],
            'email' => $fields['email'],
            'puesto' => $fields['puesto'],
            'empresa_id' => $empresa_id,
            'fecha_alta' => Date::now()->format('d/m/Y'),
        ]);
        // 3 - Usuario: crearlo y mandar email de reseteo de contraseña
        $user = $this->createNewUser(
            $trabajador->nombre . ' ' . $trabajador->apellidos,
            $trabajador->email,
            '',
            'externo',
            $trabajador->empresa_id
        );
        if ($user) {
            // 4 - Adjuntar usuario al trabajador
            $user->trabajador()->save($trabajador);
            // Guardamos el trabajador como persona de contacto del contratista
            $contrato->addPersonaContacto($empresa_id, $trabajador->id);
            // 5 - Manda el email a la persona de contacto
            Aviso::createAviso(
                'Has sido invitado a participar en el contrato ' . $contrato->getNombreAvisos() . '.',
                route('contratos.edit', $contrato->id),
                [$user->id]
            );
            $this->sendInvitacionContratistaContratoEmail(true, $trabajador, $contrato);
        }
    }

    public function detachContratistas(Request $request, $id)
    {
        $contrato = $this->currentContrato();
        $contratista_id = $request->get('c');
        if ($contratista_id == null) {
            // Contratista
            $contrato->contratistas()->detach([$id]);
            // Personas contacto
            $contrato->removePersonasContacto($id);
            // Aviso al personal emp. ppal.
            $this->sendContratistaAviso(Empresa::getNombreEmpresa($id), null, $contrato, false, false);
        //
            // TODO: quitar subcontratistas
            //
        } else {
            // Subcontratista
            DB::table('contratos_contratistas')
                ->where('contrato_id', '=', $contrato->id)
                ->where('empresa_id', '=', $contratista_id)
                ->where('subcontratista_id', '=', $id)
                ->delete();
            // Personas de contacto
            $contrato->removePersonasContacto($id);
            // Aviso al personal emp. ppal.
            $this->sendContratistaAviso(Empresa::getNombreEmpresa($contratista_id), Empresa::getNombreEmpresa($id), $contrato, false, false);
        }
        // Quitamos los documentos del contratista/subcontratista del contrato
        $contratista = Empresa::find($id);
        $docs_array = $contratista->documentos()->pluck('id')->toArray();
        $contrato->documentos()->detach($docs_array);
        //
        // TODO: quitar TRABAJADORES, quitar MAQUINAS

        Flash::success('Se ha quitado el <strong>' . ($contratista_id == null ? 'Contratista, sus <strong>subcontratistas</strong>' : 'Subcontratista') .
            ', sus <strong>documentos</strong>), <strong>trabajadores</strong> y <strong>máquinas</strong> de este contrato.');
        return array('result' => 'success');
    }

    public function contratistasLista(Request $request)
    {
        $contrato = $this->currentContrato();
        $contratista_id = $request->get('c');
        // Subcontratistas
        if ($contratista_id) {
            // El contratista que añade los sub.
            $contratista = Empresa::findOrFail($contratista_id);
            // Los sub. de este contratista que ya están en el contrato
            $subcontratistas_contrato = $contrato->subcontratistas($contratista_id)->pluck('id');
            // Subcontratistas del contatista, activos y que no estén ya en el contrato
            $empresas = $contratista->subcontratistas(true)
                                    ->whereNotIn('id', $subcontratistas_contrato);
        } else {
            // Empresa Principal
            $contratistas_contrato = $contrato->contratistas()->pluck('id');
            $empresas = Empresa::where('id', '>', '0')                      // que no sean la emp. ppal.
                               ->where('activo', '=', true)                 // activos
                               ->whereNotIn('id', $contratistas_contrato);  // que no estén ya en el contrato
        }
        $array = [];
        foreach ($empresas->orderBy('razon_social')->get() as $empresa) {
            $arr = ['id' => $empresa->id, 'text' => $empresa->displayName()];
            $array[] = $arr;
        }
        return response()->json($array);
    }

    private function sendContratistaAviso($contratista_nombre, $subcontratista_nombre, $contrato, $nuevo = false, $added = true)
    {
        $text = 'El contratista <strong>' . $contratista_nombre . '</strong> ha ';
        if ($subcontratista_nombre == null) {
            $text .= ($added == true) ? 'sido añadido ' : 'sido eliminado ';
        } else {
            $text .= (($added == true) ? 'añadido al ' . ($nuevo == true ? 'nuevo ' : '') : 'eliminado al ') . 'subcontratista <strong>' . $subcontratista_nombre . '</strong> ';
        }
        $text .= (($added == true) ? 'al' : 'del') . ' contrato ' . $contrato->getNombreAvisos();
        Aviso::createAviso(
            $text,
            route('contratos.edit', $contrato->id) . (($added == true) ? '#t6' : ''),
            $contrato->getUsuariosEmpresaPrincipal()
        );
    }
}
