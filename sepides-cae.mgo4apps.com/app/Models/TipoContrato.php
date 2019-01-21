<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoContrato extends Model
{
    protected $table = 'tipos_contratos';

    protected $fillable = [ 'nombre', 'notas', 'nivel_subcontratas', 'activo' ];

    public function tipos_documentos()
    {
        return $this->belongsToMany('App\Models\TipoDocumento', 'tipos_contratos_doc', 'tipo_contrato_id', 'tipo_documento_id')
                    ->withPivot('obligatorio');
    }

    public static function getNombreTipoContrato($tipo_id)
    {
        $tipo = TipoContrato::find($tipo_id);
        if ($tipo) {
            return $tipo->nombre;
        } else {
            return null;
        }
    }

}
