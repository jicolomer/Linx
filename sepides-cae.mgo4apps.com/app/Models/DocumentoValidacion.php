<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;


class DocumentoValidacion extends Model
{
    protected $table = 'documentos_val';

    public $timestamps = false;

    protected $fillable = [ 'documento_id', 'documento_version', 'fecha_revision', 'usuario_id', 'validado', 'notas' ];

    protected $dates = [ 'fecha_revision' ];


    public function getFechaRevisionAttribute($value)
    {
        return Date::parse($value)->format('d/m/Y H:i');
    }

}
