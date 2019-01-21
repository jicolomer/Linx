<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyContratos2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // La fecha de nacimiento del trabajador debe permitir nulos porque no
        // es obligatoria
        Schema::table('contratos', function ($table) {
            // Tipo de contrato no obligatorio
            $table->unsignedInteger('tipo_contrato_id')->nullable()->change();
            // Notas privadas
            $table->text('notas_privadas')->after('notas');
            // Eso no se usa (desde el principio!!)
            $table->dropColumn('empresa_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contratos', function ($table) {
            $table->unsignedInteger('tipo_contrato_id')->nullable(false)->change();
            $table->dropColumn('notas_privadas');
        });
    }
}
