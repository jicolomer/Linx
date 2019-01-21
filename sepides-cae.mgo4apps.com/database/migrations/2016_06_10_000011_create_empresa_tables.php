<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpresaTables extends Migration
{

    public function up()
    {
        // EMPRESAS
        Schema::create('empresas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('razon_social');
            $table->string('nombre', 10);
            $table->string('cif', 9);
            $table->string('direccion');
            $table->string('codigo_postal', 5);
            $table->string('municipio', 50);
            $table->unsignedInteger('provincia_id');
            $table->string('telefono', 9);
            $table->string('telefono2', 9);
            $table->string('fax', 9);
            $table->string('modalidad_preventiva', 3)->default('SPA');
            $table->string('codigo_cnae', 4);
            $table->boolean('construccion')->default(false);
            $table->unsignedInteger('actividad_construccion')->default(0);
            $table->boolean('plantilla_indefinida')->default(false);
            $table->string('rea', 30);
            $table->boolean('autonomo')->default(false);
            $table->boolean('trabajadores_a_cargo')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // CENTROS DE TRABAJO
        Schema::create('centros', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('empresa_id')->default(0);
            $table->string('nombre');
            $table->string('direccion');
            $table->string('codigo_postal', 5);
            $table->string('municipio', 100);
            $table->unsignedInteger('provincia_id');
            $table->string('telefono_centro', 9);
            $table->string('fax_centro', 9);
            $table->string('email_centro');
            $table->string('persona_contacto');
            $table->string('email_contacto');
            $table->string('telefono_contacto', 9);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // TRABAJADORES
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('empresa_id');
            $table->string('nombre', 50);
            $table->string('apellidos', 100);
            $table->string('nss', 12);
            $table->string('nif', 9);
            $table->dateTime('fecha_nacimiento');
            $table->string('direccion');
            $table->string('codigo_postal', 5);
            $table->string('municipio', 50);
            $table->unsignedInteger('provincia_id');
            $table->string('telefono', 9);
            $table->string('telefono2', 9);
            $table->string('email');
            $table->string('puesto', 50);
            $table->boolean('recurso_preventivo')->default(false);
            $table->boolean('delegado_prevencion')->default(false);
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->dateTime('fecha_alta');
            $table->dateTime('fecha_baja')->nullable();
            $table->boolean('activo')->default(true);
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
        Schema::dropIfExists('empresas');
        Schema::dropIfExists('centros');
        Schema::dropIfExists('trabajadores');
    }
}
