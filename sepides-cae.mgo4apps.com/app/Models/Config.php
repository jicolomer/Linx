<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Config extends Model
{
    protected $fillable = [
        'nombre_corto', 'logo', 'logo_small', 'mimes_permitidos', 'invitar_subcontratistas',
        'caducidad_m_dias', 'caducidad_t_dias', 'caducidad_s_dias', 'caducidad_a_dias', 'caducidad_v_dias',
        'filas_tablas', 'filas_tablas_modal'
    ];


    // Carga la configuración de la base de datos a la caché 'config'
    public static function loadConfig()
    {
        $cfg = Config::first();

        config(['cae.nombre_corto' => $cfg->nombre_corto]);
        config(['cae.logo' => $cfg->logo]);
        config(['cae.logo_small' => $cfg->logo_small]);
        config(['cae.mimes_permitidos' => $cfg->mimes_permitidos]);
        config(['cae.invitar_subcontratistas' => $cfg->invitar_subcontratistas]);
        config(['cae.caducidad_m_dias' => $cfg->caducidad_m_dias]);
        config(['cae.caducidad_t_dias' => $cfg->caducidad_t_dias]);
        config(['cae.caducidad_s_dias' => $cfg->caducidad_s_dias]);
        config(['cae.caducidad_a_dias' => $cfg->caducidad_a_dias]);
        config(['cae.caducidad_v_dias' => $cfg->caducidad_v_dias]);
        config(['cae.filas_tablas' => $cfg->filas_tablas]);
        config(['cae.filas_tablas_modal' => $cfg->filas_tablas_modal]);
    }
}
