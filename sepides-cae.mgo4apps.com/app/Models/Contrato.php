<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

use Jenssegers\Date\Date;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

use App\User;
use App\Models\Trabajador;
use App\Models\ContratoTrabajador;
use App\Models\Maquina;
use App\Models\ContratoMaquina;

class Contrato extends Model implements HasMediaConversions
{
    use HasMediaTrait;

    public $documentos_pivot_table = 'contratos_doc';

    protected $fillable = [ 'nombre', 'referencia', 'tipo_contrato_id', 'notas', 'notas_privadas',
                            'fecha_firma', 'fecha_inicio_obras', 'fecha_fin_obras', 'importe_contrato',
                            'responsable_contrato_id', 'tecnico_encargado_id', 'tecnico_encargado2_id',
                            'tecnico_prl_id', 'coordinador_cap_id', 'tecnico_averias_id' ];

    protected $dates = [ 'created_at', 'updated_at', 'fecha_firma', 'fecha_inicio_obras', 'fecha_fin_obras' ];

    protected $casts = [
        'responsable_contrato_id' => 'integer',
        'tecnico_encargado_id' => 'integer',
        'tecnico_encargado2_id' => 'integer',
        'coordinador_cap_id' => 'integer',
        'tecnico_prl_id' => 'integer',
    ];


    // *************************************************************************
    // RELACIONES
    // *************************************************************************
    // Centros
    public function centros()
    {
        return $this->belongsToMany('App\Models\Centro', 'contratos_centros', 'contrato_id', 'centro_id');
    }
    // Contratistas
    public function contratistas($onlyContratistas = true)
    {
        if ($onlyContratistas) {
            return $this->belongsToMany('App\Models\Empresa', 'contratos_contratistas', 'contrato_id', 'empresa_id')->wherePivot('subcontratista_id', 0);
        } else {
            return $this->belongsToMany('App\Models\Empresa', 'contratos_contratistas', 'contrato_id', 'empresa_id')->withPivot('subcontratista_id');
        }
    }
    // Subcontratistas
    public function subcontratistas($contratista_id = null)
    {
        if ($contratista_id) {
            return $this->belongsToMany('App\Models\Empresa', 'contratos_contratistas', 'contrato_id', 'subcontratista_id')
                        ->where('empresa_id', $contratista_id)
                        ->wherePivot('subcontratista_id', '>', 0)
                        ->withPivot('empresa_id');
        } else {
            return $this->belongsToMany('App\Models\Empresa', 'contratos_contratistas', 'contrato_id', 'subcontratista_id')
                        ->wherePivot('subcontratista_id', '>', 0)
                        ->withPivot('empresa_id');
        }
    }
    // Contratista/s del subcontratista
    public function contratistas_subcontratista($subcontratista_id)
    {
        $contratistas = DB::table('contratos_contratistas')
                          ->groupBy('contrato_id')
                          ->groupBy('empresa_id')
                          ->groupBy('subcontratista_id')
                          ->having('contrato_id', '=', $this->id)
                          ->having('subcontratista_id', '=', $subcontratista_id);

        return Empresa::whereIn('id', $contratistas->pluck('empresa_id'));
    }
    // Analiza si el subcontratista es subcontratista de un determindado contratista
    public function is_subcontratista_of($subcontratista_id, $contratista_id)
    {
        return ($this->subcontratistas($contratista_id)->where('subcontratista_id', $subcontratista_id)->count() > 0);
    }
    // Documentación requerida
    public function tipos_documentos()
    {
        return $this->belongsToMany('App\Models\TipoDocumento', 'contratos_doc_requerida', 'contrato_id', 'tipo_documento_id')->withPivot('obligatorio');
    }
    // Documentos
    public function documentos()
    {
        return $this->belongsToMany('App\Models\Documento', $this->documentos_pivot_table, 'contrato_id', 'documento_id');
    }
    // Trabajadores
    public function trabajadores($empresa_id, $centro_id = null)
    {
        $ids = ContratoTrabajador::leftJoin('trabajadores', 'contratos_trabajadores.trabajador_id', '=', 'trabajadores.id')
                                 ->where('contrato_id', '=', $this->id)
                                 ->where('empresa_id', '=', $empresa_id);
        if ($centro_id != null) {
            $ids = $ids->where('centro_id', '=', $centro_id);
        }
        $ids = $ids->distinct()->get()->pluck('trabajador_id');

        return Trabajador::whereIn('id', $ids);
    }
    // Máquinas
    public function maquinas($empresa_id, $centro_id = null)
    {
        $ids = ContratoMaquina::leftJoin('maquinas', 'contratos_maquinas.maquina_id', '=', 'maquinas.id')
                                 ->where('contrato_id', '=', $this->id)
                                 ->where('empresa_id', '=', $empresa_id);
        if ($centro_id != null) {
            $ids = $ids->where('centro_id', '=', $centro_id);
        }
        $ids = $ids->distinct()->get()->pluck('maquina_id');

        return Maquina::whereIn('id', $ids);
    }


    // *************************************************************************
    // HELPERS
    // *************************************************************************
    // DOCUMENTACIÓN REQUERIDA
    // Añade los tipos de documentos del Tipo de Contrato como Documentación Requerida
    public function addDocumentacionRequerida()
    {
        $tipo_contrato = TipoContrato::find($this->tipo_contrato_id);
        if ($tipo_contrato) {
            $count = 0;
            $doc_requerida = $this->tipos_documentos();
            foreach ($tipo_contrato->tipos_documentos()->get() as $tipo_documento) {
                $doc_requerida->attach($tipo_documento->id, ['obligatorio' => $tipo_documento->pivot->obligatorio]);
                $count++;
            }
            return $count;
        }
        return false;
    }

    // Adjunta todos los documentos requeridos para el contrato de un modelo:
    //      Empresa Principal, Centro,
    //      Contratista, Subcontratista,
    //      Trabajador y Máquina
    public function addDocumentosRequeridos($ambito, $model, $field_name)
    {
        $count = 0;
        $documentos_contrato = $this->documentos();
        $doc_requerida_ambito = $this->tipos_documentos()->where('ambito', '=', $ambito)->get();

        foreach ($doc_requerida_ambito as $tipo_documento) {
            $documentos_model = $model->documentos()->where('tipo_documento_id', '=', $tipo_documento->id);
            foreach ($documentos_model->get() as $documento) {
                $pivot_data = [$field_name => $model->id];
                // Esta excepción no me gusta ponerla aquí pero es lo más rápido.
                // Los centros son siempre de la empresa principal.
                if ($ambito == 'CEN') {
                    $pivot_data['empresa_id'] = 0;
                }
                $documentos_contrato->syncWithoutDetaching([$documento->id => $pivot_data]);
                $count++;
            }
        }

        return $count;
    }


    // STATUS DOCUMENTACIÓN
    // Status global documentación contrato: Contratistas+Subcontratistas+Trabajadores+Máquinas
    public function statusDocContrato()
    {
        $contratistas = $this->contratistas(false);
        if ($contratistas->count() == 0) {
            // No hay contratistas
            return -1;
        }

        $status = 0;
        foreach ($contratistas->get() as $empresa) {
            $status_empresa = $this->statusDocContratista($empresa->id);
            if ($status_empresa == -2) {
                // Falta documentación
                return -2;
            }
            $status = $this->analizaStatusDoc($status, $status_empresa);

            // TRABAJADORES/MÁQUINAS
            $trabajadores = $this->trabajadores($empresa->id);
            $maquinas = $this->maquinas($empresa->id);
            if (($trabajadores->count() == 0) && ($maquinas->count() == 0)) {
                // No hay ni trabajadores ni máquinas, devolvemos que falta doc.
                return -2;
            }
            // Trabajadores
            $status_trabajadores = $this->statusDocTrabajadoresContratista($empresa->id);
            if ($status_trabajadores == -2) {
                // Falta documentación
                return -2;
            }
            $status = $this->analizaStatusDoc($status, $status_trabajadores);
            // Máquinas
            $status_maquinas = $this->statusDocMaquinasContratista($empresa->id);
            if ($status_maquinas == -2) {
                // Falta documentación
                return -2;
            }
            $status = $this->analizaStatusDoc($status, $status_maquinas);
        }

        return $status;
    }
    // Status documentación Empresa (ámbito CTA) del contratista
    public function statusDocContratista($contratista_id, $incluir_subcontratistas = false)
    {
        return $this->statusDoc($contratista_id, 'empresa_id', 'CTA');
    }
    // Status global trabajadores del contratista
    public function statusDocTrabajadoresContratista($contratista_id, $incluir_subcontratistas = false)
    {
        return $this->statusDocTrabajadoresMaquinasContratista(true, $contratista_id, $incluir_subcontratistas);
    }
    // Status global máquinas del contratista
    public function statusDocMaquinasContratista($contratista_id, $incluir_subcontratistas = false)
    {
        return $this->statusDocTrabajadoresMaquinasContratista(false, $contratista_id, $incluir_subcontratistas);
    }
    // Este e el método que analiza el estado de la documentación de un trabajador o máquina
    private function statusDocTrabajadoresMaquinasContratista($isTrabajador, $contratista_id, $incluir_subcontratistas = false)
    {
        if ($isTrabajador) {
            $ids = $this->trabajadores($contratista_id)->get()->pluck('id');
        } else {
            $ids = $this->maquinas($contratista_id)->get()->pluck('id');
        }
        // No hay Tra/Maq
        if ($ids->count() == 0) {
            return -1;
        }
        $status = 0;
        foreach ($ids as $id) {
            if ($isTrabajador) {
                $s = $this->statusDocTrabajador($id);
            } else {
                $s = $this->statusDocMaquina($id);
            }

            if ($s == -2) {
                // Si falta documentación salimos inmediatamente
                return -2;
            }

            $status = $this->analizaStatusDoc($status, $s);
        }

        return $status;
    }
    // Status documentación del trabajador
    public function statusDocTrabajador($trabajador_id, $returnSiNoHayDocumentos = false)
    {
        return $this->statusDoc($trabajador_id, 'trabajador_id', 'TRA', $returnSiNoHayDocumentos);
    }
    // Status documentación de la máquina
    public function statusDocMaquina($maquina_id, $returnSiNoHayDocumentos = false)
    {
        return $this->statusDoc($maquina_id, 'maquina_id', 'MAQ', $returnSiNoHayDocumentos);
    }
    //
    private function statusDoc($item_id, $nombreCampoPivot, $ambito, $returnSiNoHayDocumentos = false)
    {
        $documentos = $this->documentos()->wherePivot($nombreCampoPivot, '=', $item_id)->get();
        // No hay documentos!
        if ($returnSiNoHayDocumentos && ($documentos->count() == 0)) {
            return -1;
        }
        // Documentación requerida OBLIGATORIA
        $falta_doc = $this->tipos_documentos()
                          ->wherePivot('obligatorio', '=', true)
                          ->where('ambito', '=', $ambito)
                          ->whereNotIn('id', $documentos->pluck('tipo_documento_id'))
                          ->count() > 0;
        if ($falta_doc) {
            return -2;
        }

        $status = 0;
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

        return $status;
    }
    // Analiza el status y devuelve según prioridades
    private function analizaStatusDoc($status, $new_status)
    {
        if ($new_status == -2) {
            // Falta doc.
            return $new_status;
        }

        if (($new_status < -3) && ($status == 0)) {
            // No validado
            return $new_status;
        } else if (($new_status > 0) && ($status == 0 || $status < -3)) {
            // Próximo a caducar
            return $new_status;
        } else if (($new_status == -3) && ($status == 0 || $status < -3 || $status > 0)) {
            // Caducado
            return $new_status;
        }

        return $status;
    }


    // AVISOS
    // Devuelve un array con los trabajadores de la empresa principal en el contrato
    public function getTrabajadoresEmpresaPrincipal()
    {
        return [
            $this->responsable_contrato_id,
            $this->tecnico_encargado_id,
            $this->tecnico_encargado2_id,
            $this->coordinador_cap_id,
            $this->tecnico_prl_id
        ];
    }
    // Devuelve un array con los usuarios de la empresa principal en el contrato
    // (responsable contrato, técnicos encargados, técnico de prevención, etc.)
    // Se usa para AVISOS
    public function getUsuariosEmpresaPrincipal()
    {
        $arr_trabajadores = $this->getTrabajadoresEmpresaPrincipal();

        $users = Trabajador::whereIn('id', array_filter($arr_trabajadores))->pluck('user_id')->toArray();

        return array_filter($users);
    }
    // Nombre del contrato para los avisos
    public function getNombreAvisos()
    {
        return '<strong>' . $this->nombre . '</strong> <em>(REF. ' . $this->referencia . ')</em>';
    }


    // PERSONAS CONTACTO CONTRATISTAS
    // Guarda el trabajador como persona de contacto con el contratista para este contrato
    public function addPersonaContacto($contratista_id, $trabajador_id)
    {
        DB::table('contratos_personas_contacto')->insert([
            'contrato_id' => $this->id,
            'empresa_id' => $contratista_id,
            'trabajador_id' => $trabajador_id
        ]);

        // Añadimos a personas de contacto de la empresa sólo si no está ya.
        if (Empresa::find($contratista_id)->personas_contacto()->where('trabajador_id', $trabajador_id)->count() == 0) {
            DB::table('empresas_personas_contacto')->insert([
                'empresa_id' => $contratista_id,
                'trabajador_id' => $trabajador_id
            ]);
        }
    }
    // Elimina las personas de contacto del contratista del contrato
    public function removePersonasContacto($contratista_id)
    {
        DB::table('contratos_personas_contacto')
            ->where('contrato_id', '=', $this->id)
            ->where('empresa_id', '=', $contratista_id)
            ->delete();
    }
    // Devuelve las personas de contacto del contratista/sub. para este contrato
    // Devuelve App\User
    public function personas_contacto($contratista_id = null)
    {
        $contactos = DB::table('contratos_personas_contacto')->where('contrato_id', '=', $this->id);
        if ($contratista_id != null) {
            $contactos = $contactos->where('empresa_id', '=', $contratista_id);
        }
        $contactos_id = $contactos->pluck('trabajador_id');
        $users_id = Trabajador::whereIn('id', $contactos_id)
                        ->pluck('user_id');

        return User::whereIn('id', $users_id);
    }


    // *************************************************************************
    // DATES
    // *************************************************************************
    public function getFechaFirmaAttribute($value)
    {
        if ($value) {
            return Date::parse($value)->format('d/m/Y');
        } else {
            return null;
        }
    }
    public function setFechaFirmaAttribute($value)
    {
        if (is_string($value)) {
            if (empty($value)) {
                $value = null;
            } else {
                $value = Date::createFromFormat('d/m/Y', $value);
            }
        }
        $this->attributes['fecha_firma'] = $value;
    }
    public function getFechaInicioObrasAttribute($value)
    {
        if ($value) {
            return Date::parse($value)->format('d/m/Y');
        } else {
            return null;
        }
    }
    public function setFechaInicioObrasAttribute($value)
    {
        if (is_string($value)) {
            if (empty($value)) {
                $value = null;
            } else {
                $value = Date::createFromFormat('d/m/Y', $value);
            }
        }
        $this->attributes['fecha_inicio_obras'] = $value;
    }
    public function getFechaFinObrasAttribute($value)
    {
        if ($value) {
            return Date::parse($value)->format('d/m/Y');
        } else {
            return null;
        }
    }
    public function setFechaFinObrasAttribute($value)
    {
        if (is_string($value)) {
            if (empty($value)) {
                $value = null;
            } else {
                $value = Date::createFromFormat('d/m/Y', $value);
            }
        }
        $this->attributes['fecha_fin_obras'] = $value;
    }


    // *************************************************************************
    // MEDIA CONVERSIONS
    // *************************************************************************
    public function registerMediaConversions()
    {
        $this->addMediaConversion('thumb')
             ->width(200)
             ->nonQueued();
    }

}
