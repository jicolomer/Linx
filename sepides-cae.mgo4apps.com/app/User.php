<?php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Yajra\Acl\Traits\HasRole;

use Yajra\Acl\Models\Role;

class User extends Authenticatable
{
    use Notifiable, HasRole;

    protected $fillable = [ 'nombre', 'email', 'telefono', 'password', 'empresa_id', 'centro_id', 'activo' ];

    protected $hidden = [ 'password', 'remember_token' ];


    public function trabajador()
    {
        return $this->hasOne('App\Models\Trabajador');
    }

    public function avisos()
    {
        return $this->belongsToMany('App\Models\Aviso', 'avisos_users');
    }

    public function isTrabajador()
    {
        return ($this->trabajador()->get()->count() == 1);
    }

    // Devuelve si el usuario se considera de la empresa principal para permitirle
    // acciones o no
    public function isPrincipal()
    {
        return $this->isAdministrador() || $this->isTecnico() || $this->isResponsable();
    }

    public function randomizePassword()
    {
        $this->password = bcrypt(str_random());
        return $this;
    }

    // Busca un rol por su slug
    // Devuelve una instancia del modelo Role
    public static function findRoleBySlug($slug)
    {
        $role = Role::where('slug', '=', $slug)->first();
        if ($role) {
            return $role;
        } else {
            throw new ModelNotFoundException();
        }
    }

    public function getCreatedAt($value)
    {
        return Jenssegers\Date\Date::parse($value)->format('d/m/Y');
    }

}
