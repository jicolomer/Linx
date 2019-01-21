<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaquinariaTables extends Migration
{

    public function up()
    {
        // TIPOS DE MAQUINAS
        Schema::create('tipos_maquinas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100);
            $table->text('notas');
            $table->timestamps();
        });

        // MAQUINAS
        Schema::create('maquinas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('tipo_maquina_id');
            $table->string('nombre', 100);
            $table->string('marca');
            $table->string('modelo');
            $table->string('matricula', 20);
            $table->string('num_serie', 50)->nullable();
            $table->string('num_bastidor', 50)->nullable();
            $table->unsignedInteger('anio_fabricacion')->nullable();
            $table->dateTime('fecha_alta');
            $table->dateTime('fecha_baja')->nullable();
            $table->text('notas');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('tipos_maquinas');
        Schema::dropIfExists('maquinas');
    }
}
