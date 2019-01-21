<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;

use Jenssegers\Date\Date;

class Documento extends Model
{
    use Taggable;

    protected $fillable = [
        'tipo_documento_id', 'nombre', 'fecha_documento', 'fecha_caducidad',
        'notas', 'version', 'filename', 'original_filename', 'mime', 'activo'
    ];

    protected $dates = [ 'created_at', 'updated_at', 'fecha_documento', 'fecha_caducidad' ];

    // Versiones del documento
    public function versiones()
    {
        return DocumentoVersion::where('id', '=', $this->id);
    }

    // RELACIONES INVERSAS
    // Devuelve los contratos a los que está adjuntado el documento
    public function contratos()
    {
        return $this->belongsToMany('App\Models\Contrato', 'contratos_doc', 'documento_id', 'contrato_id');
    }

    public function empresa()
    {
        return $this->belongsToMany('App\Models\Empresa', 'empresas_doc', 'documento_id', 'empresa_id')->first();
    }

    public function trabajador()
    {
        return $this->belongsToMany('App\Models\Trabajador', 'trabajadores_doc', 'documento_id', 'trabajador_id')->withPivot('tipo_documento_trabajador', 'horas_formacion');
    }

    public function maquina()
    {
        return $this->belongsToMany('App\Models\Maquina', 'maquinas_doc', 'documento_id', 'maquina_id');
    }

    public function tipo_documento()
    {
        if ($this->tipo_documento_id) {
            $tipo = TipoDocumento::find($this->tipo_documento_id);
            return $tipo;
        }

        return null;
    }

    public function ambito()
    {
        if ($tipo = $this->tipo_documento()) {
            return $tipo->ambito;
        }

        return null;
    }


    // El tipo de caducidad del documento->tipo_documento->tipo_caducidad
    public function tipoCaducidad()
    {
        if ($tipo = $this->tipo_documento()) {
            return $tipo->tipo_caducidad;
        }

        return null;
    }

    // El tipo de caducidad pero en texto legible 'Anual', 'Mensual'...
    public function tipoCaducidadString()
    {
        if ($tipo_caducidad = $this->tipoCaducidad()) {
            return config('enums.tipos_caducidad')[$tipo_caducidad];
        }
    }

    // El estatus en cuanto a caducidad del documento:
    //
    //     -2 = No tiene fecha caducidad
    //     -1 = OK
    //      0 = Caducado!
    //      n = Días que faltan para caducar -> En naranja
    //
    public function statusCaducidad()
    {
        if ($tipo_caducidad = $this->tipoCaducidad()) {
            $dias_vencimiento = 30;
            switch ($tipo_caducidad) {
                case 'M':
                    $dias_vencimiento = config('cae.caducidad_m_dias');
                    break;
                case 'T':
                    $dias_vencimiento = config('cae.caducidad_t_dias');
                    break;
                case 'S':
                    $dias_vencimiento = config('cae.caducidad_s_dias');
                    break;
                case 'A':
                    $dias_vencimiento = config('cae.caducidad_a_dias');
                    break;
                case 'V':
                    $dias_vencimiento = config('cae.caducidad_v_dias');
                    break;
                case 'N':
                    // No vence nunca
                    return -2;
                    break;
                default:
                    break;
            }

            if ($fecha_caducidad = $this->fecha_caducidad) {
                $fecha_caducidad = Date::createFromFormat('d/m/Y', $fecha_caducidad);
                if (Date::now()->gte($fecha_caducidad)) {
                    // Caducado!
                    return 0;
                }
                $dias_faltan = Date::now()->diffInDays($fecha_caducidad);
                // dd($fecha_caducidad, $dias_vencimiento, $dias_faltan);
                if ($dias_faltan <= $dias_vencimiento) {
                    return $dias_faltan;
                } else {
                    return -1;
                }
            }

            return -2;
        }
    }



    // Devuelve el status de la validación actual del documento
    //
    //      -1 = Documento rechazado
    //       0 = Documeno NO evaludado
    //       1 = Documento APROBADO
    //
    public function statusValidacion()
    {
        if ($val = $this->validacion()) {
            return ($val->aprobado == true) ? 1 : -1;
        } else {
            return 0;
        }
    }

    // Devuelve el registro documento_val
    public function validacion()
    {
        if ($this->validacion_id == null) {
            return null;
        }
        $val = DocumentoValidacion::find($this->validacion_id);
        if ($val) {
            return $val;
        } else {
            // No existe. Algo ha ido mal así que marcamos el documento como
            // pendiente de validar.
            $this->validacion_id = null;
            $this->save();
            return null;
        }
    }

    public function getFechaDocumentoAttribute($value)
    {
        return Date::parse($value)->format('d/m/Y');
    }

    public function setFechaDocumentoAttribute($value)
    {
        $this->attributes['fecha_documento'] = Date::createFromFormat('d/m/Y', $value);
    }

    public function getFechaCaducidadAttribute($value)
    {
        if ($value) {
            return Date::parse($value)->format('d/m/Y');
        } else {
            return null;
        }
    }

    public function setFechaCaducidadAttribute($value)
    {
        if ($value) {
            $this->attributes['fecha_caducidad'] = Date::createFromFormat('d/m/Y', $value);
        } else {
            $this->attributes['fecha_caducidad'] = null;
        }
    }
}
