<?php
namespace App\Http\Controllers\Base;

use Illuminate\Http\Request;
use Auth;

use Laracasts\Flash\Flash;
use Yajra\Acl\Models\Role;

use App\User;

trait UserMethodsTrait
{
    // Devuelve lista de roles de usuarios permitidos para el usuario actual y
    // la empresa
    public function rolesPermitidos($empresa_principal = false)
    {
        $roles = Role::pluck('name', 'slug')->toArray();
        $rol_actual = Auth::user()->roles()->first()->slug;

        if ($empresa_principal == true) {
            unset($roles['externo']);
        } else {
            return [User::findRoleBySlug('externo')->id => $roles['externo']];
        }

        switch ($rol_actual) {
            case 'administrador':
            case 'externo':
                break;

            case 'responsable':
            case 'tecnico':
                unset($roles['administrador']);
                break;

            case 'control':
            default:
                $roles = [];
        }

        return Role::whereIn('slug', array_keys($roles))->pluck('name', 'id');
    }

    // Crrea un nuevo usuario con los parámetros pasados.
    // Contraseña aleatoria para enviar posteriormente el email
    public function createNewUser($name, $email, $telefono, $rol_id, $empresa_id)
    {
        $user = User::create([
                        'nombre' => $name,
                        'email' => $email,
                        'telefono' => $telefono,
                        'empresa_id' => $empresa_id,
                        'password' => bcrypt(str_random()),
        ]);
        if (is_numeric($rol_id)) {
            $user->assignRole($rol_id);
        } else {
            $user->attachRoleBySlug($rol_id);
        }
        $user->save();

        return $user;
    }

    // Llamado con un GET desde la vista
    public function blockUser($id)
    {
        $user = $this->resetPassword($id);

        if ($user) {
            Flash::success('¡Se ha bloqueado el acceso al usuario!<br>Para darle acceso de nuevo tendrá que resetear su contraseña.');
        } else {
            Flash::error('¡Ha ocurrido un error y no se ha podido bloquear el acceso del usuario!<br>(ERROR: No se ha encontrado el usuario #' . $id . ')');
        }

        return redirect()->back();
    }

    // Resetea la contraseña de un usuario (metiendo basura aleatoria)
    public function randomizePassword($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->randomizePassword()->save();
            return $user;
        } else {
            return null;
        }
    }

}
