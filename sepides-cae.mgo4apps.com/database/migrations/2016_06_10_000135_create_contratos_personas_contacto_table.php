<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContratosPersonasContactoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contratos_personas_contacto', function (Blueprint $table) {
            $table->unsignedInteger('contrato_id');
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('trabajador_id');

            $table->unique(['contrato_id', 'empresa_id', 'trabajador_id'], 'pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contratos_personas_contacto');
    }
}
