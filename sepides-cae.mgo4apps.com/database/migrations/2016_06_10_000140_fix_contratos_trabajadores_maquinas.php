<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixContratosTrabajadoresMaquinas extends Migration
{
    public function up()
    {
        // Elimino las tablas existentes que no sirven
        $this->down();

        // CONTRATOS - TRABAJADORES ASIGNADOS
        $this->createAssociativeTable('contratos_trabajadores', 'trabajador_id');

        // CONTRATOS - MAQUINARIA ASIGNADA
        $this->createAssociativeTable('contratos_maquinas', 'maquina_id');

    }

    public function down()
    {
        Schema::dropIfExists('contratos_trabajadores');
        Schema::dropIfExists('contratos_maquinas');
    }

    private function createAssociativeTable($tableName, $fieldName)
    {
        Schema::create($tableName, function (Blueprint $table) use($fieldName) {
            $table->unsignedInteger('contrato_id');
            $table->unsignedInteger('centro_id');
            $table->unsignedInteger($fieldName);
            $table->date('fecha_inicio_trabajos');
            $table->date('fecha_fin_trabajos')->nullable();
            $table->boolean('trabaja_lunes')->default(false);
            $table->boolean('trabaja_martes')->default(false);
            $table->boolean('trabaja_miercoles')->default(false);
            $table->boolean('trabaja_jueves')->default(false);
            $table->boolean('trabaja_viernes')->default(false);
            $table->boolean('trabaja_sabado')->default(false);
            $table->boolean('trabaja_domingo')->default(false);
            $table->boolean('permiso_status')->nullable();         // null = No evaluado, 1 - Aceptado/OK, 0 - Rechazado
            $table->string('permiso_motivo_rechazo')->nullable();   // Si es rechazado hay que poner el motivo
            $table->unsignedInteger('permiso_user_id')->nullable(); // Usuario que evalúa el permiso
            $table->dateTime('permiso_fecha')->nullable();  // Fecha de la evaluación del permiso

            $table->unique(['contrato_id', 'centro_id', $fieldName, 'fecha_inicio_trabajos'], 'pk');
        });
    }

}
