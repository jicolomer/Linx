<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

use App\Models\TipoMaquina;
use App\Models\TipoDocumento;

class Maquina extends Model
{
    protected $fillable = [ 'empresa_id', 'tipo_maquina_id',
                            'nombre', 'marca', 'modelo',
                            'matricula', 'num_serie', 'num_bastidor',
                            'anio_fabricacion', 'marcado_ce', 'conformidad_rd',
                            'fecha_alta', 'fecha_baja', 'notas', 'activo',
    ];

    protected $dates = [ 'fecha_alta', 'fecha_baja', 'created_at', 'updated_at' ];

    public function nombreMaquina($withId = false)
    {
        $nombre = $this->nombre;
        if ($withId) {
            $nombre .= ' (#' . $this->id . ')';
        }
        return $nombre;
    }

    // Función estática que busca la máquina y devuelve el nombre + id.
    public static function getNombreMaquina($maquina_id, $withId = true)
    {
        $maquina = Maquina::find($maquina_id);
        if ($maquina) {
            return $maquina->nombreMaquina($withId);
        } else {
            return ' ';
        }
    }

    // TIPO MAQUINA - DOCUMENTOS
    public function tiposDocumentos()
    {
        $tipo_maquina = TipoMaquina::find($this->tipo_maquina_id);
        $tipos_documentos = $tipo_maquina->tipos_documentos();
        return $tipos_documentos;
    }

    public function tiposDocumentosFaltantes()
    {
        $faltan = [];

        $tipos_documentos = $this->tiposDocumentos()->get();
        foreach ($tipos_documentos as $td) {
            if ($td->pivot->obligatorio == true) {
                // Obligatorio!
                $documentos = $this->documentos()->where('activo', '=', true);
                if ($documentos->where('tipo_documento_id', '=', $td->id)->count() == 0) {
                    // No existe
                    $faltan[] = $td->id;
                }
            } else {
                $faltan[] = $td->id;
            }
        }

        $tipos = $this->tiposDocumentos()->whereIn('id', $faltan);
        return $tipos;
    }

    // DOCUMENTOS
    public $documentos_pivot_table = 'maquinas_doc';

    public function documentos()
    {
        return $this->belongsToMany('App\Models\Documento', $this->documentos_pivot_table, 'maquina_id', 'documento_id');
    }

    /*
     * Devuelve el estatus de la documentación del trabajador
     *
     *      >0: Documento con caducidad próxima ($status == número de días hasta caducidad)
     *       0: Todo OK
     *      -1: No hay documentos
     *      -2: Falta doc. obligatoria
     *      -3: Documento Caducado
     *      -4: Pendiente de Validar
     *      -5: Documento rechazado
     *
     */
    public function statusDocumentacion()
    {
        $status = 0;

        $documentos = $this->documentos()->where('activo', '=', true);
        // No hay documentos
        if ($documentos->count() == 0) {
            return -1;
        }

        // Primero comprobamos si están los documentos obligatorios según el tipo
        // de máquina.
        $tipos_documentos = $this->tiposDocumentos()->wherePivot('obligatorio', '=', true)->get();
        foreach ($tipos_documentos as $td) {
            $documentos = $this->documentos()->where('activo', '=', true);
            if ($documentos->where('tipo_documento_id', '=', $td->id)->count() == 0) {
                // Falta doc. obligatorio
                return -2;
            }
        }

        // Ahora miramos la vigencia y validación de los documentos
        $documentos = $this->documentos()->where('activo', '=', true)->get();
        foreach ($documentos as $documento) {
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


    public function getFechaAltaAttribute($value)
    {
        return Date::parse($value)->format('d/m/Y');
    }

    public function setFechaAltaAttribute($value)
    {
        $this->attributes['fecha_alta'] = Date::createFromFormat('d/m/Y', $value);
    }

    public function getFechaBajaAttribute($value)
    {
        return Date::parse($value)->format('d/m/Y');
    }

    public function setFechaBajaAttribute($value)
    {
        $this->attributes['fecha_baja'] = Date::createFromFormat('d/m/Y', $value);
    }
}
