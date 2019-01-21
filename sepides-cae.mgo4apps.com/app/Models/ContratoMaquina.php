<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;


class ContratoMaquina extends Model
{
    protected $table = 'contratos_maquinas';

    protected $fillable = [
        'contrato_id', 'centro_id', 'maquina_id', 'fecha_inicio_trabajos', 'fecha_fin_trabajos',
        'trabaja_lunes', 'trabaja_martes', 'trabaja_miercoles', 'trabaja_jueves', 'trabaja_viernes', 'trabaja_sabado', 'trabaja_domingo',
        'permiso_status', 'permiso_motivo_rechazo', 'permiso_user_id', 'permiso_fecha'
    ];

    public $timestamps = false;

    protected $dates = [ 'fecha_inicio_trabajos', 'fecha_fin_trabajos', 'fecha_permiso' ];


    public function getFechaInicioTrabajosAttribute($value)
    {
        return Date::parse($value)->format('d/m/Y');
    }

    public function setFechaInicioTrabajosAttribute($value)
    {
        if (is_string($value)) {
            if (empty($value)) {
                $value = null;
            } else {
                $value = Date::createFromFormat('d/m/Y', $value);
            }
        }

        $this->attributes['fecha_inicio_trabajos'] = $value;
    }

    public function getFechaFinTrabajosAttribute($value)
    {
        return Date::parse($value)->format('d/m/Y');
    }

    public function setFechaFinTrabajosAttribute($value)
    {
        if (is_string($value)) {
            if (empty($value)) {
                $value = null;
            } else {
                $value = Date::createFromFormat('d/m/Y', $value);
            }
        }

        $this->attributes['fecha_fin_trabajos'] = $value;
    }

    public function getPermisoFechaAttribute($value)
    {
        if ($value) {
            return Date::parse($value)->format('d/m/Y H:i');
        } else {
            return null;
        }
    }

}
