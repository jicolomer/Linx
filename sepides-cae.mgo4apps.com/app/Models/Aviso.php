<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;


class Aviso extends Model
{
    protected $fillable = [ 'texto', 'url', 'mostrado', 'leido' ];

    protected $dates = [ 'created_at', 'updated_at' ];


    public function users()
    {
        return $this->belongsToMany('App\User', 'avisos_users');
    }

    public static function createAviso($texto, $url, $users_array, $send_email = false)
    {
        $aviso = new Aviso();
        $aviso->texto = $texto;
        $aviso->url = $url;
        if (! is_array($users_array)) {
            $users_array = $users_array->toArray();
        }
        $users = array_filter($users_array);

        // Quitamos al usuario actualmente logueado de los avisos: no necesita
        // ser informado, ya lo sabe
        if (in_array(Auth::user()->id, $users)) {
            $users = array_diff($users, [Auth::user()->id]);
        }

        // Quitamos posibles duplicados
        $users = array_unique($users);

        DB::transaction(function () use ($aviso, $users) {

            $aviso->save();
            $aviso->users()->attach($users);

        });

        // TODO: send email!
        if ($send_email == true) {
            // Envía un email inmediato al usuario.
        }
    }

}
