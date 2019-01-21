<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdContratosTrabajadoresMaquinas extends Migration
{
    public function up()
    {
        Schema::table('contratos_trabajadores', function ($table) {
            $table->increments('id')->first();
        });
        Schema::table('contratos_maquinas', function ($table) {
            $table->increments('id')->first();
        });
    }

    public function down()
    {
        Schema::table('contratos_trabajadores', function ($table) {
            $table->dropColumn('id');
        });
        Schema::table('contratos_maquinas', function ($table) {
            $table->dropColumn('id');
        });
    }
}
