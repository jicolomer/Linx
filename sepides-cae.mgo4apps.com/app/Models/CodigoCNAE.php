<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoCNAE extends Model
{
    protected $table = 'codigos_cnae';

    protected $fillable = [ 'descripcion'];
}
