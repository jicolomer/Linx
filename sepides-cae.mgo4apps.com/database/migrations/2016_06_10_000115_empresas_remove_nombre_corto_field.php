<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmpresasRemoveNombreCortoField extends Migration
{
    public function up()
    {
        Schema::table('empresas', function ($table) {
            $table->dropColumn('nombre');
        });
    }

    public function down()
    {
        Schema::table('empresas', function ($table) {
            $table->string('nombre', 10);
        });
    }
}
