<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $fillable = [ 'nombre' ];

    public static function getNombre($provincia_id)
    {
        $provincia = Provincia::find($provincia_id);
        if ($provincia) {
            return $provincia->nombre;
        } else {
            return ' ';
        }
    }
}
