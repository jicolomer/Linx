<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyContratosTable extends Migration
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
            $table->dateTime('fecha_firma')->nullable()->change();
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
            $table->dateTime('fecha_firma')->nullable(false)->change();
        });
    }
}
