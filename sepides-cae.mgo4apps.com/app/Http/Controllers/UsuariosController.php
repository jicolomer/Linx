<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Auth;
use Validator;
use DB;

use Yajra\Datatables\Datatables;
use Jenssegers\Date\Date;
use Laracasts\Flash\Flash;

use Yajra\Acl\Models\Role;
use Yajra\Acl\Models\Permission;

use App\User;
use App\Models\Empresa;

class UsuariosController extends Base\BaseController
{
    use Base\UserMethodsTrait, Base\EmailsTrait;

    public function __construct()
    {
        parent::__construct(User::class, 'usuarios');
    }

    // public function index()

    public function create(Request $request)
    {
        $user_roles = Role::pluck('name', 'id');

        return parent::__create($request, compact('user_roles'));
    }

    public function store(Request $request)
    {
        // La contraseña que viene hay que encriptarla para guardarla
        if ($pass = $request->get('password')) {
            if ($conf = $request->get('password_confirmation')) {
                if ($pass === $conf) {
                    $pw = bcrypt($pass);
                    $request['password'] = $pw;
                    $request['password_confirmation'] = $pw;
                }
            }
        }

        $user = parent::__store_create_record($request);
        // Error en validación
        if ($user == false) {
            return redirect()->back()
                        ->with('errors', $this->validator_instance->errors())
                        ->withInput();
        }

        // Rol
        $user->assignRole($request['rol']);

        return parent::__store_return();
    }

    public function edit(Request $request, $id)
    {
        $user = User::find($id);
        $role_id = $user->roles()->first()->id;
        $is_trabajador = $user->isTrabajador();

        $empresa_nombre = Empresa::getNombreEmpresa($user->empresa_id);
        $user_roles = Role::pluck('name', 'id');

        return parent::__edit($request, $id, compact('is_trabajador', 'empresa_nombre', 'user_roles', 'role_id'));
    }

    public function update(Request $request, $id)
    {
        $user = parent::__update_save_record($request, $id);
        // Error en validación
        if ($user == false) {
            return redirect()->back()
                        ->with('errors', $this->validator_instance->errors())
                        ->withInput();
        }

        // Rol
        $user->syncRoles([$request['rol']]);
        $user->save();

        return parent::__update_return();
    }

    public function remove($id)
    {
        $result = $this->__remove($id);

        // Reseteamos la contraseña del usuario para que ya no pueda acceder al
        // sistema
        if ($result['result'] == 'success') {
            $this->randomizePassword($id);
        }

        return $result;
    }


    public function rowsData()
    {
        $archive = request()->get('h');

        $users = User::where('activo', '=', !$archive);

        if (Auth::user()->empresa_id > 0) {
            $users = $users->where('empresa_id', '=', Auth::user()->empresa_id);
        }

        return Datatables::of($users)
                    ->editColumn('created_at', function ($user) {
                        return Date::parse($user->created_at)->format('d/m/Y');
                    })
                    ->addColumn('actions', function ($user) use ($archive) {
                        return $this->getActionColumn($user, true, $archive);  // BaseController
                    })
                    ->rawColumns(['actions'])
                    ->editColumn('rol', function ($user) {
                        $role = $user->roles()->first();
                        if ($role) {
                            return $role->name;
                        } else {
                            return ' ';
                        }
                    })
                    ->make(true);
    }

    protected function validator(array $data, $isUpdate = false)
    {
        $validator = Validator::make($data, [
            'nombre' => 'required|max:100',
            'email' => 'required|email|max:255|' . ($isUpdate ? 'unique:users,email,'.$data['id'] : 'unique:users'),
            'nif' => 'min:9|max:9' . ($isUpdate ? 'unique:users,nif,'.$data['id'] : 'users:trabajadores'),
            'telefono' => 'digits:9',
            'password' => ($isUpdate ? '' : 'required|') . 'confirmed|min:6',
            'rol' => 'required',
        ]);

        $fields_names = [
            'nombre' => 'Nombre',
            'email' => 'Email',
            'nif' => 'NIF/DNI',
            'telefono' => 'Teléfono',
            'password' => 'Contraseña',
            'rol' => 'Rol'
        ];
        $validator->setAttributeNames($fields_names);

        return $validator;
    }

    // Resetea la contraseña de un usuario y le manda el correspondiente email
    // GET usuarios/reset-password/{id}
    public function resetPassword($id)
    {
        $user = $this->randomizePassword($id);

        if ($user) {
            $this->sendResetUserPasswordEmail($user);
            Flash::success('Se ha reseteado la contraseña del usuario.<br>Recibirá un email para crear una nueva contraseña.');
        } else {
            Flash::error('¡Ha ocurrido un error y no se ha podido resetear la contraseña del usuario!<br>(ERROR: No se ha encontrado el usuario #' . $id . ')');
        }

        return redirect()->back();
    }

    // Devuelve un JSON con la lista de usuarios de una empresa
    // NOTA: de momento no se usa. Se usa el metodo correspondiente en Trabajadores
    public function json_ListaUsuariosEmpresa($empresa_id)
    {
        $usuarios = User::where('empresa_id', '=', $empresa_id)->where('activo', '=', true)->pluck('nombre', 'id');

        $array = [];
        foreach ($usuarios as $key => $value) {
            $arr = ['id' => $key, 'text' => $value ];
            $array[] = $arr;
        }

        return response()->json($array);
    }

    // Muestra la pantalla para modificar permisos
    public function permisosIndex()
    {
        $roles = Role::pluck('name', 'id');

        return view('usuarios.permissions', compact('roles'));
    }

    // Modifica un permiso (on/off)
    public function cambiaPermiso(Request $request)
    {
        if ($permiso_id = $request->get('p')) {
            if ($role_id = $request->get('r')) {
                if ($checked = $request->get('c')) {
                    $permission = Permission::findOrFail($permiso_id);
                    if ($checked === 'true') {
                        $permission->assignRole($role_id);
                        $permission->save();
                    } else {
                        $permission->revokeRole($role_id);
                        $permission->save();
                    }
                    return [
                    'result' => 'success',
                    'msg' => 'Se ha <strong>' . ($checked === 'true' ? 'añadido' : 'revocado') . '</strong> el permiso correctamente.'
                    ];
                }
            }
        }

        return [
        'result' => 'error',
        'msg' => 'No se ha podido cambiar el permiso.'
        ];
    }

    public function permisosData()
    {
        $data = [];
        $permisos = Permission::orderBy('resource')->get();
        $roles = Role::pluck('id');

        foreach ($permisos as $permiso) {
            $row = [
            'rowId' => $permiso->id,
            'tipo' => $permiso->resource,
            'permiso' => $permiso->name
            ];
            foreach ($roles as $key => $role_id) {
                $row['rol_' . $role_id] = $this->permisoColumn($permiso->id, $role_id, false);
            }
            foreach ($permiso->roles()->get() as $permisoRole) {
                $row['rol_' . $permisoRole->id] = $this->permisoColumn($permiso->id, $permisoRole->id, true);
            }
            $data[] = $row;
        }

        return ['data' => $data];
    }
    private function permisoColumn($permiso_id, $role_id, $checked)
    {
        if (Auth::user()->can('permisos.update')) {
            $name = '"permiso_' . $permiso_id . '_' . $role_id . '"';
            $html = '<input id=' . $name . ' name=' . $name . ' type="checkbox" ';
            if ($checked) {
                $html .= 'checked ';
            }
            return $html . '/>';
        } else {
            if ($checked) {
                return '<p style="font-size:18px"><i class="fa fa-check text-green"></i><p>';
            } else {
                return ' ';
            }
        }
    }
}
