<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpreasPersonasContactoTable extends Migration
{
    public function up()
    {
        Schema::create('empresas_personas_contacto', function (Blueprint $table) {
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('trabajador_id');

            $table->primary(['empresa_id', 'trabajador_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('empresas_personas_contacto');
    }
}
