<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMaquina extends Model
{
    protected $table = 'tipos_maquinas';

    protected $fillable = [ 'nombre', 'notas' ];

    public function tipos_documentos()
    {
        return $this->belongsToMany('App\Models\TipoDocumento', 'tipos_maquinas_doc', 'tipo_maquina_id', 'tipo_documento_id')
                    ->withPivot('obligatorio');
    }
}
