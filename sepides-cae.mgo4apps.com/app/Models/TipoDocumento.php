<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;

class TipoDocumento extends Model
{
    use Taggable;

    protected $table = 'tipos_documentos';

    protected $fillable = [ 'nombre', 'referencia', 'notas', 'tipo_caducidad', 'ambito', 'activo' ];

}
