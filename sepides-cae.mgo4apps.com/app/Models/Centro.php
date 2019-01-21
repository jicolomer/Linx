<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Centro extends Model
{
    public $documentos_pivot_table = 'centros_doc';

    protected $fillable = [
        'nombre', 'direccion', 'codigo_postal', 'municipio', 'provincia_id',
        'telefono_centro', 'fax_centro', 'email_centro',
        'persona_contacto', 'telefono_contacto', 'email_contacto',
        'activo'
    ];

    public function documentos()
    {
        return $this->belongsToMany('App\Models\Documento', $this->documentos_pivot_table, 'centro_id', 'documento_id');
    }

}
