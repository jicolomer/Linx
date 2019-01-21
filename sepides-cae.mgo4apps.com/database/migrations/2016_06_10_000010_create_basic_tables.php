<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBasicTables extends Migration
{

    public function up()
    {
        // PROVINCIAS
        Schema::create('provincias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100);
        });

        // CODIGOS CNAE
        Schema::create('codigos_cnae', function (Blueprint $table) {
            $table->string('codigo', 4)->primary();
            $table->string('descripcion');
        });

        // TIPOS DOCUMENTOS
        Schema::create('tipos_documentos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100);
            $table->string('referencia', 50);
            $table->text('notas');
            $table->string('ambito', 3);
            $table->string('tipo_caducidad', 1)->default('N');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // TIPOS CONTRATOS
        Schema::create('tipos_contratos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 150)->unique();
            $table->text('notas');
            $table->boolean('nivel_subcontratas')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('provincias');
        Schema::dropIfExists('codigos_cnae');
        Schema::dropIfExists('tipos_documentos');
        Schema::dropIfExists('tipos_contratos');
    }
}
