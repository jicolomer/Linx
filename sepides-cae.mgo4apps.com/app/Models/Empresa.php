<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;
use DB;

use App\Models\Contrato;


class Empresa extends Model
{
    public $documentos_pivot_table = 'empresas_doc';

    protected $fillable = [ 'razon_social', 'cif', 'direccion', 'codigo_postal', 'municipio', 'provincia_id',
                            'telefono', 'telefono2', 'fax', 'modalidad_preventiva', 'codigo_cnae',
                            'construccion', 'actividad_construccion', 'plantilla_indefinida', 'rea',
                            'autonomo', 'trabajadores_a_cargo', 'activo' ];


    // *************************************************************************
    // RELACIONES
    // *************************************************************************

    public function trabajadores()
    {
        return $this->hasMany('App\Models\Trabajador');
    }

    public function maquinas()
    {
        return $this->hasMany('App\Models\Maquina');
    }

    public function documentos()
    {
        return $this->belongsToMany('App\Models\Documento', $this->documentos_pivot_table, 'empresa_id', 'documento_id');
    }

    public function contratos()
    {
        $contratos = DB::table('contratos_contratistas')
                            ->where('empresa_id', '=', $this->id)
                            ->orWhere('subcontratista_id', '=', $this->id)
                            ->distinct()
                            ->pluck('contrato_id');

        return Contrato::whereIn('id', $contratos);
    }

    public function subcontratistas($activo = true)
    {
        $subcontratistas = DB::table('contratistas_subcontratistas')
                                ->where('contratista_id', '=', $this->id)
                                ->pluck('subcontratista_id');

        return Empresa::whereIn('id', $subcontratistas)->where('activo', '=', $activo);
    }

    public static function addSubcontratista($contratista_id, $subcontratista_id)
    {
        $subcontratista = DB::table('contratistas_subcontratistas')
                            ->where('contratista_id', '=', $contratista_id)
                            ->where('subcontratista_id', '=', $subcontratista_id);

        if ($subcontratista->count() == 0) {
            DB::table('contratistas_subcontratistas')->insert([
                'contratista_id' => $contratista_id,
                'subcontratista_id' => $subcontratista_id
            ]);
        }
    }

    public function personas_contacto()
    {
        return $this->belongsToMany('App\Models\Trabajador', 'empresas_personas_contacto', 'empresa_id', 'trabajador_id');
    }




    // *************************************************************************
    // HELPERS
    // *************************************************************************

    public static function getNombreEmpresa($empresa_id)
    {
        $empresa_nombre = null;

        if ($empresa_id >= 0) {
            $empresa = Empresa::find($empresa_id);
            if ($empresa) {
                return $empresa->displayName();
            }
        }

        return '';
    }

    public function displayName()
    {
        return $this->razon_social . ' (#' . $this->id . ')';
    }

    /**
     *  Devuelve la lista de empresas externas para usarla en campos 'select'
     */
    public static function getExternasList($activo = true)
    {
        $externas = Empresa::where('id', '>', '0')->where('activo', '=', $activo)->orderBy('razon_social')->get();

        $list = [];
        foreach ($externas as $empresa) {
            $list[$empresa->id] = $empresa->displayName();
        }

        return $list;
    }



    public function setCifAttribute($value)
    {
        if ($value) {
            $value = strtoupper($value);
        }

        $this->attributes['cif'] = $value;
    }

}
