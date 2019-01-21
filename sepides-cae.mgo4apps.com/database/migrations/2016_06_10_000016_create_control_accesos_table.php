<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlAccesosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // CONTROL ACCESSOS (donde se guardan los permisos)
        Schema::create('control_accesos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipo', 1);
            $table->unsignedInteger('tipo_id');
            $table->unsignedInteger('centro_id');
            $table->boolean('permiso')->default(false);
            $table->unsignedInteger('registro_permiso_id')->nullable();
            $table->timestamps();
        });

        // CONTROL ACCESSOS - REGISTRO DE PERMISOS
        Schema::create('control_accesos_registro_permisos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('permiso_id');
            $table->boolean('permiso');
            $table->unsignedInteger('usuario_id');  // Usuario que da o quita el permiso. 0 = Sistema (caducidades)
            $table->text('motivo_permiso');
            $table->timestamps();
        });

        // CONTROL ACCESSOS - REGISTRO DE ACCESOS
        Schema::create('control_accesos_registro', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipo', 1);
            $table->unsignedInteger('permiso_id');
            $table->unsignedInteger('centro_id');
            $table->boolean('accede');
            $table->unsignedInteger('usuario_id');  // Usuario que da el acceso
            $table->text('notas_acceso');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('control_accesos');
        Schema::dropIfExists('control_accesos_registro_permisos');
        Schema::dropIfExists('control_accesos_registro');
    }
}
