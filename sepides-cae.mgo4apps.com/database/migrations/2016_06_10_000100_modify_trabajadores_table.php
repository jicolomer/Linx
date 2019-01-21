<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTrabajadoresTable extends Migration
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
        Schema::table('trabajadores', function ($table) {
            $table->dateTime('fecha_nacimiento')->nullable()->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trabajadores', function ($table) {
            $table->dateTime('fecha_nacimiento')->nullable(false)->change();
        });
    }
}
