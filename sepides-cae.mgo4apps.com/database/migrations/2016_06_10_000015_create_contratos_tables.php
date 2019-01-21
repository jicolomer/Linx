<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContratosTables extends Migration
{
    public function up()
    {
        // CONTRATOS
        Schema::create('contratos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tipo_contrato_id');
            $table->string('referencia', 30);
            $table->string('nombre');
            $table->text('notas');
            $table->dateTime('fecha_firma');
            $table->dateTime('fecha_inicio_obras')->nullable();
            $table->dateTime('fecha_fin_obras')->nullable();
            $table->decimal('importe_contrato', 15, 2);
            $table->unsignedInteger('empresa_principal_id')->default(0);
            $table->unsignedInteger('responsable_contrato_id');
            $table->unsignedInteger('tecnico_encargado_id')->nullable();
            $table->unsignedInteger('tecnico_encargado2_id')->nullable();
            $table->unsignedInteger('tecnico_prl_id');
            $table->unsignedInteger('coordinador_cap_id');
            $table->unsignedInteger('tecnico_averias_id')->nullable();
            $table->unsignedInteger('empresa_id')->nullable();
            $table->timestamps();
        });

        // CONTRATOS - CENTROS
        Schema::create('contratos_centros', function (Blueprint $table) {
            $table->unsignedInteger('contrato_id');
            $table->unsignedInteger('centro_id');

            $table->unique(['contrato_id', 'centro_id'], 'pk');
        });

        // CONTRATOS - CONTRATISTAS
        Schema::create('contratos_contratistas', function (Blueprint $table) {
            $table->unsignedInteger('contrato_id');
            $table->unsignedInteger('empresa_id');
            $table->unsignedInteger('subcontratista_id')->default(0);

            $table->unique(['contrato_id', 'empresa_id', 'subcontratista_id'], 'pk');
        });

        // CONTRATOS - DOCUMENTACIÓN REQUERIDA
        Schema::create('contratos_doc_requerida', function (Blueprint $table) {
            $table->unsignedInteger('contrato_id');
            $table->unsignedInteger('tipo_documento_id');
            $table->boolean('obligatorio')->default(true);

            $table->unique(['contrato_id', 'tipo_documento_id'], 'pk');
        });

        // CONTRATOS - DOCUMENTOS ASOCIADOS
        Schema::create('contratos_doc', function (Blueprint $table) {
            $table->unsignedInteger('contrato_id');
            $table->unsignedInteger('documento_id');
            $table->unsignedInteger('empresa_id')->nullable();
            $table->unsignedInteger('centro_id')->nullable();
            $table->unsignedInteger('trabajador_id')->nullable();
            $table->unsignedInteger('maquina_id')->nullable();

            $table->unique(['contrato_id', 'documento_id'], 'pk');
        });

        // CONTRATOS - TRABAJADORES ASIGNADOS
        Schema::create('contratos_trabajadores', function (Blueprint $table) {
            $table->unsignedInteger('contrato_id');
            $table->unsignedInteger('centro_id');
            $table->unsignedInteger('trabajador_id');
            $table->dateTime('fecha_inicio_trabajos')->nullable();
            $table->dateTime('fecha_fin_trabajos')->nullable();
            $table->boolean('trabaja_lunes')->default(true);
            $table->boolean('trabaja_martes')->default(true);
            $table->boolean('trabaja_miercoles')->default(true);
            $table->boolean('trabaja_jueves')->default(true);
            $table->boolean('trabaja_viernes')->default(true);
            $table->boolean('trabaja_sabado')->default(false);
            $table->boolean('trabaja_domingo')->default(false);
            $table->unsignedInteger('permiso');  // 0 - No evaluado, 1 - Aceptado/OK, 2 - Rechazado
            $table->string('motivo_rechazo');

            $table->unique(['contrato_id', 'centro_id', 'trabajador_id'], 'pk');
        });

        // CONTRATOS - MAQUINARIA ASIGNADA
        Schema::create('contratos_maquinas', function (Blueprint $table) {
            $table->unsignedInteger('contrato_id');
            $table->unsignedInteger('centro_id');
            $table->unsignedInteger('maquina_id');
            $table->dateTime('fecha_inicio_trabajos')->nullable();
            $table->dateTime('fecha_fin_trabajos')->nullable();
            $table->boolean('trabaja_lunes')->default(true);
            $table->boolean('trabaja_martes')->default(true);
            $table->boolean('trabaja_miercoles')->default(true);
            $table->boolean('trabaja_jueves')->default(true);
            $table->boolean('trabaja_viernes')->default(true);
            $table->boolean('trabaja_sabado')->default(false);
            $table->boolean('trabaja_domingo')->default(false);
            $table->unsignedInteger('permiso');  // 0 - No evaluado, 1 - Aceptado/OK, 2 - Rechazado
            $table->string('motivo_rechazo');

            $table->unique(['contrato_id', 'centro_id', 'maquina_id'], 'pk');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contratos');
        Schema::dropIfExists('contratos_centros');
        Schema::dropIfExists('contratos_contratistas');
        Schema::dropIfExists('contratos_doc_requerida');
        Schema::dropIfExists('contratos_doc');
        Schema::dropIfExists('contratos_trabajadores');
        Schema::dropIfExists('contratos_maquinas');
    }
}
