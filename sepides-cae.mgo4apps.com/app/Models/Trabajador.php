<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class Trabajador extends Model
{
    protected $table = 'trabajadores';

    protected $fillable = [ 'nombre', 'apellidos', 'nif', 'nss', 'fecha_nacimiento',
                            'direccion', 'codigo_postal', 'municipio', 'provincia_id',
                            'telefono', 'telefono2', 'email',
                            'puesto', 'recurso_preventivo', 'delegado_prevencion',
                            'empresa_id', 'fecha_alta', 'fecha_baja', 'user_id', 'activo' ];


    // Nombre completo del trabajador Apellidos, Nombre
    public function nombreCompleto($withId = false)
    {
        $nombre = $this->apellidos . ', ' . $this->nombre;
        if ($withId) {
            $nombre .= ' (#' . $this->id . ')';
        }
        return $nombre;
    }

    // Función estática que busca el trabajador y devuelve el nombre.
    public static function getNombreTrabajador($trabajador_id, $withId = false)
    {
        $trabajador = Trabajador::find($trabajador_id);
        if ($trabajador) {
            return $trabajador->nombreCompleto($withId);
        } else {
            return ' ';
        }
    }


    // USUARIO
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function hasUser()
    {
        return ($this->user()->get()->count() == 1);
    }




    // DOCUMENTOS
    public $documentos_pivot_table = 'trabajadores_doc';

    public function documentos($tipo = '')
    {
        $documentos = $this->belongsToMany('App\Models\Documento', $this->documentos_pivot_table, 'trabajador_id', 'documento_id')
            ->withPivot('tipo_documento_trabajador', 'horas_formacion');
        if ($tipo != '') {
            $documentos = $documentos->wherePivot('tipo_documento_trabajador', $tipo);
        }

        return $documentos;
    }

    /*
     * Devuelve el estatus de la documentación del trabajador
     *
     *      >0: Documento con caducidad próxima ($status == número de días hasta caducidad)
     *       0: Todo OK
     *      -1: No hay documentos
     *      -2: Falta doc. obligatoria (no existe en el caso del trabajador)
     *      -3: Documento Caducado
     *      -4: Pendiente de Validar
     *      -5: Documento rechazado
     *
     */
    public function statusDocumentacion($tipo = '')
    {
        $status = 0;
        $documentos = $this->documentos($tipo)->where('activo', '=', true);

        // No hay documentos
        if ($documentos->count() == 0) {
            return -1;
        }

        foreach ($documentos->get() as $documento) {
            $status_caducidad = $documento->statusCaducidad();
            if ($status_caducidad == 0) {
                // Documento caducado
                return -3;
            } else if ($status_caducidad > 0) {
                // Próximo a caducar
                return $status_caducidad;
            }
            $status_validacion = $documento->statusValidacion();
            if ($status_validacion < 1) {
                // Documento no evaluado o rechazado
                return ($status_validacion == 0) ? -4 : -5;
            }
        }

        return $status; // OK
    }



    // FECHAS
    protected $dates = [ 'created_at', 'updated_at', 'fecha_nacimiento', 'fecha_alta', 'fecha_baja' ];

    public function getFechaNacimientoAttribute($value)
    {
        if ($value == null) {
            return null;
        }

        return Date::parse($value)->format('d/m/Y');
    }

    public function setFechaNacimientoAttribute($value)
    {
        if (is_string($value)) {
            $value = empty($value) ? null : Date::createFromFormat('d/m/Y', $value);
        }
        $this->attributes['fecha_nacimiento'] = $value;
    }

    public function getFechaAltaAttribute($value)
    {
        return Date::parse($value)->format('d/m/Y');
    }

    public function setFechaAltaAttribute($value)
    {
        if (is_string($value)) {
            $value = Date::createFromFormat('d/m/Y', $value);
        }
        $this->attributes['fecha_alta'] = $value;
    }

    public function getFechaBajaAttribute($value)
    {
        return Date::parse($value)->format('d/m/Y');
    }

    public function setFechaBajaAttribute($value)
    {
        if (is_string($value)) {
            $value = Date::createFromFormat('d/m/Y', $value);
        }
        $this->attributes['fecha_baja'] = $value;
    }
}
