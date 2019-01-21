<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Validator;
use DB;
use Flash;

use Yajra\Datatables\Datatables;

use App\Models\Trabajador;
use App\Models\TipoDocumento;
use App\Models\Empresa;
use App\Models\Provincia;
use App\User;

class TrabajadoresController extends Documentos\DocumentosBaseController
{
    use Base\UserMethodsTrait, Base\EmailsTrait;

    public function __construct()
    {
        parent::__construct(Trabajador::class, 'trabajadores', 'trabajador');
    }

    // public function index()

    public function create(Request $request)
    {
        $empresa_id = parent::getEmpresaIdFromRequest($request);
        $user_roles = $this->rolesPermitidos($empresa_id === 0);
        $provincias = Provincia::pluck('nombre', 'id');

        $empresas = null;
        $empresa_nombre = null;
        if ($empresa_id === 0 || $empresa_id > 0) {
            $empresa_nombre = Empresa::getNombreEmpresa($empresa_id);
        } else {
            $empresas = Empresa::getExternasList();
        }

        return parent::__create($request, compact('user_roles', 'provincias', 'empresas', 'empresa_id', 'empresa_nombre'));
    }

    public function store(Request $request)
    {
        $res = $this->saveTrabajador($request);

        if (is_array($res)) {
            $trabajador = parent::__store_create_record($request, $res['data']);
            // Error de validación
            if ($trabajador == false) {
                return redirect()->back()
                            ->with('errors', $this->validator_instance->errors())
                            ->withInput();
            }

            return parent::__store_return($this->saveTrabajadorFinish($res, $trabajador));
        } else {
            return $res;
        }
    }

    public function edit(Request $request, $id)
    {
        $users = User::pluck('nombre', 'id');
        $provincias = Provincia::pluck('nombre', 'id');

        $trabajador = Trabajador::find($id);
        $empresa_nombre = Empresa::getNombreEmpresa($trabajador->empresa_id);
        $user_roles = $this->rolesPermitidos($trabajador->empresa_id === 0);
        $ambito = "TRA";

        return parent::__edit($request, $id, compact('users', 'user_roles', 'provincias', 'empresa_nombre', 'ambito'));
    }

    public function update(Request $request, $id)
    {
        $res = $this->saveTrabajador($request, $id);
        // Si no es un array es un error de validación
        if (is_array($res)) {
            $trabajador = parent::__update_save_record($request, $id, $res['data']);
            // Error de validación
            if ($trabajador == false) {
                return redirect()->back()
                            ->with('errors', $this->validator_instance->errors())
                            ->withInput();
            }
            if ($trabajador->hasUser() == true) {
                $fields['nombre'] = $trabajador->nombre . ' ' . $trabajador->apellidos;
                $fields['email'] = $trabajador->email;
                $fields['telefono'] = $trabajador->telefono;
                $fields['empresa_id'] = $trabajador->empresa_id;
                $user = $trabajador->user()->get()[0];
                $user->fill($fields)->save();

                $user->syncRoles([$request->user_rol]);
                $user->save();

                return parent::__update_return('Datos del usuario sincronizados.');
            } else {
                return parent::__update_return($this->saveTrabajadorFinish($res, $trabajador));
            }
        } else {
            // Error al validar datos del usuario
            return $res;
        }
    }

    public function remove($id)
    {
        $trabajador = Trabajador::findOrFail($id);
        $trabajador->activo = false;
        $trabajador->save();

        // Reseteamos la contraseña del usuario (si lo tiene)
        // para que ya no pueda acceder al sistema
        if ($trabajador->user_id != null) {
            $this->randomizePassword($trabajador->user_id);
        }

        // Si tiene documentos se marcan como archivados
        $documentos = $trabajador->documentos()->get();
        foreach ($documentos as $documento) {
            $documento->activo = false;
            $documento->save();
        }

        Flash::success('El trabajador ha sido dado de baja correctamente.');

        return ['result' => 'success'];
    }

    public function rowsData(Request $request)
    {
        $trabajadores = Trabajador::join('empresas', 'trabajadores.empresa_id', '=', 'empresas.id')
                        ->select([ 'trabajadores.*',
                                    DB::raw("CONCAT(empresas.razon_social, ', (#', empresas.id, ')') AS empresa") ])
                        ->where('trabajadores.activo', '=', true);

        if (isset($request['e'])) {
            $empresa_id = $request->get('e');
            $trabajadores->where('trabajadores.empresa_id', '=', $empresa_id);
        } else {
            $trabajadores->where('trabajadores.empresa_id', '>', 0);
        }

        $datatable = Datatables::of($trabajadores)
            ->setRowId('id')
            ->removeColumn('created_at')
            ->removeColumn('updated_at')
            ->addColumn('is_recurso', function ($trabajador) {
                return $this->getCheckColumn($trabajador->recurso_preventivo);
            })
            ->addColumn('is_delegado', function ($trabajador) {
                return $this->getCheckColumn($trabajador->delegado_prevencion);
            })
            ->addColumn('status_formacion', function ($trabajador) {
                return $this->getDocStatusColumn($trabajador->statusDocumentacion('FOR'));
            })
            ->addColumn('status_informacion', function ($trabajador) {
                return $this->getDocStatusColumn($trabajador->statusDocumentacion('INF'));
            })
            ->addColumn('status_epis', function ($trabajador) {
                return $this->getDocStatusColumn($trabajador->statusDocumentacion('EPI'));
            })
            ->addColumn('status_salud', function ($trabajador) {
                return $this->getDocStatusColumn($trabajador->statusDocumentacion('VIS'));
            })
            ->addColumn('status_otros', function ($trabajador) {
                return $this->getDocStatusColumn($trabajador->statusDocumentacion('OTR'));
            })
            ->rawColumns(['is_recurso','is_delegado','status_formacion','status_informacion','status_epis','status_salud','status_otros']);

        return $datatable->make(true);
    }

    protected function validator(array $data, $isUpdate = false)
    {
        $rules = [
            'nombre' => 'required|max:50',
            'apellidos' => 'required|max:100',
            'empresa_id' => 'required|min:1',
            'nif' => 'required|min:9|max:9|' . ($isUpdate ? 'unique:trabajadores,nif,'.$data['id'] : 'unique:trabajadores'),
            'nss' => 'digits:12|' . ($isUpdate ? 'unique:trabajadores,nss,'.$data['id'] : 'unique:trabajadores'),
            'fecha_nacimiento' => 'date_format:d/m/Y',
            'direccion' => 'max:255',
            'codigo_postal' => 'digits:5',
            'municipio' => 'max:50',
            'provincia_id' => 'min:1',
            'telefono' => 'digits:9',
            'telefono2' => 'digits:9',
            'email' => 'email|max:255',
            'puesto' => 'required|max:50',
            'fecha_alta' => 'required|date_format:d/m/Y',
        ];

        if (isset($data['crear_usuario']) || isset($data['usuario_id'])) {
            $rules['user_rol'] = 'required|min:1';
            $rules['email'] = 'required|email|max:255';
        }

        $validator = Validator::make($data, $rules);

        $fields_names = [
            'nombre' => 'Nombre',
            'apellidos' => 'Apellidos',
            'empresa_id' => 'Empresa',
            'nif' => 'NIF/DNI',
            'nss' => 'Nº Seg. Social',
            'fecha_nacimiento' => 'Fecha Nacimiento',
            'direccion' => 'Dirección',
            'codigo_postal' => 'Código Postal',
            'municipio' => 'Municipio',
            'provincia_id' => 'Provincia',
            'telefono' => 'Teléfono',
            'telefono2' => 'Teléfono 2',
            'email' => 'Email',
            'puesto' => 'Puesto de trabajo',
            'fecha_alta' => 'Fecha Alta',
            'user_rol' => 'Rol de usuario',
        ];
        $validator->setAttributeNames($fields_names);

        return $validator;
    }

    private function saveTrabajador(Request $request, $id = null)
    {
        $recurso_preventivo = $request->exists('recurso_preventivo');
        $delegado_prevencion = $request->exists('delegado_prevencion');
        $data = compact('recurso_preventivo', 'delegado_prevencion');

        $usuario_id = null;
        $crear_usuario = $request->exists('crear_usuario');
        if ($crear_usuario == true) {
            $res = $this->crearNuevoUsuario($request, $id);
            if (is_integer($res)) {
                $usuario_id = $res;
            } else {
                return $res;
            }
        }

        return compact('crear_usuario', 'data', 'usuario_id');
    }

    private function crearNuevoUsuario(Request $request, $id = null)
    {
        $fields = $request->all();

        $update = ($id != null);
        $this->validator_instance = $this->validator($fields, $update);
        if ($this->validator_instance->fails()) {
            return redirect()->back()
                        ->with('errors', $this->validator_instance->errors())
                        ->withInput();
        }

        $user = $this->createNewUser(
            $fields['nombre'] . ' ' . $fields['apellidos'],
            $fields['email'],
            $fields['telefono'],
            $fields['user_rol'],
            $fields['empresa_id']
        );

        if ($user) {
            $this->sendResetUserPasswordEmail($user);
        }

        return $user->id;
    }

    private function saveTrabajadorFinish($res, $trabajador)
    {
        $msg = '';

        if ($res['crear_usuario'] == true) {
            $user = User::findOrFail($res['usuario_id']);
            $user->trabajador()->save($trabajador);
            $msg = 'Nuevo usuario creado.';
        }

        return $msg;
    }

    // Devuelve un JSON con la lista de trabajadores de la empresa que tienen usuario
    // asociado. Se usa para enviar emails a estas personas de contacto.
    public function json_ListaTrabajadoresUsuariosEmpresa($empresa_id)
    {
        $trabajadores = Trabajador::where('empresa_id', $empresa_id)->where('activo', true)->get();

        $array = [];
        foreach ($trabajadores as $trabajador) {
            if ($trabajador->hasUser()) {
                $arr = ['id' => $trabajador->id, 'text' => $trabajador->apellidos . ', ' . $trabajador->nombre . ' (' . $trabajador->puesto . ')' ];
                $array[] = $arr;
            }
        }

        return response()->json($array);
    }
}
