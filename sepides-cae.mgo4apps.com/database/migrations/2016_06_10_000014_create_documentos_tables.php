<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentosTables extends Migration
{
    public function up()
    {
        // DOCUMENTOS
        $this->createDocumentosTable('documentos', true);

        // DOCUMENTOS - VERSIONES
        $this->createDocumentosTable('documentos_ver', false);

        // DOCUMENTOS - VALIDACIONES
        Schema::create('documentos_val', function (Blueprint $table) {
            $table->increments('id');  // ID de la validación
            $table->unsignedInteger('documento_id');
            $table->unsignedInteger('documento_version');
            $table->dateTime('fecha_revision');
            $table->unsignedInteger('usuario_id');  // Usuario que ha realizado la revisión
            $table->boolean('aprobado')->default(false);
            $table->text('notas');
        });

        // DOCUMENTOS EMPRESAS
        Schema::create('empresas_doc', function (Blueprint $table) {
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('documento_id');

            $table->unique(['empresa_id', 'documento_id'], 'pk');
        });

        // DOCUMENTOS CENTROS
        Schema::create('centros_doc', function (Blueprint $table) {
            $table->unsignedInteger('centro_id');
            $table->unsignedInteger('documento_id');

            $table->unique(['centro_id', 'documento_id'], 'pk');
        });

        // DOCUMENTOS TRABAJADORES
        Schema::create('trabajadores_doc', function (Blueprint $table) {
            $table->unsignedInteger('trabajador_id');
            $table->unsignedInteger('documento_id');
            $table->string('tipo_documento_trabajador', 3)->default('OTR');  // Formación, Información, EPIS, etc.
            $table->unsignedInteger('horas_formacion');                      // Sólo para el caso de la formación

            $table->unique(['trabajador_id', 'documento_id'], 'pk');
        });

        // TIPOS DE MAQUINAS - DOCUMENTOS ASOCIADOS
        Schema::create('tipos_maquinas_doc', function (Blueprint $table) {
            $table->unsignedInteger('tipo_maquina_id');
            $table->unsignedInteger('tipo_documento_id');
            $table->boolean('obligatorio')->default(true);

            $table->unique(['tipo_maquina_id', 'tipo_documento_id'], 'pk');
        });

        // DOCUMENTOS MAQUINAS
        Schema::create('maquinas_doc', function (Blueprint $table) {
            $table->unsignedInteger('maquina_id');
            $table->unsignedInteger('documento_id');

            $table->unique(['maquina_id', 'documento_id'], 'pk');
        });

        // TIPOS CONTRATOS - DOCUMENTOS ASOCIADOS
        Schema::create('tipos_contratos_doc', function (Blueprint $table) {
            $table->unsignedInteger('tipo_contrato_id');
            $table->unsignedInteger('tipo_documento_id');
            $table->boolean('obligatorio')->default(true);

            $table->unique([ 'tipo_contrato_id', 'tipo_documento_id'], 'pk');
        });

    }

    public function down()
    {
        Schema::dropIfExists('documentos');
        Schema::dropIfExists('documentos_ver');
        Schema::dropIfExists('documentos_val');
        Schema::dropIfExists('empresas_doc');
        Schema::dropIfExists('centros_doc');
        Schema::dropIfExists('trabajadores_doc');
        Schema::dropIfExists('tipos_maquinas_doc');
        Schema::dropIfExists('maquinas_doc');
        Schema::dropIfExists('tipos_contratos_doc');
    }


    private function createDocumentosTable($table_name, $autoincrement_id) {
        Schema::create($table_name, function (Blueprint $table) use ($autoincrement_id) {

            if ($autoincrement_id == true) {
                $table->increments('id');
            } else {
                $table->unsignedInteger('id');
            }

            $table->unsignedInteger('version');
            $table->unsignedInteger('tipo_documento_id');
            $table->string('nombre', 100);
            $table->dateTime('fecha_documento');
            $table->dateTime('fecha_caducidad')->nullable();
            $table->text('notas');
            $table->string('filename');
            $table->string('mime');
            $table->string('original_filename');
            $table->unsignedInteger('validacion_id')->nullable();  // Si es nulo no ha sido revisado
            $table->boolean('activo')->default(true);
            $table->timestamps();

            if ($autoincrement_id == false) {
                $table->unique(['id', 'version'], 'pk');
            }

        });

    }
}
