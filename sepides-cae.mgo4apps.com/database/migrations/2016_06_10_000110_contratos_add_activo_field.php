<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ContratosAddActivoField extends Migration
{
    public function up()
    {
        Schema::table('contratos', function ($table) {
            $table->boolean('activo')->default(true)->after('empresa_id');
        });
    }

    public function down()
    {
        Schema::table('contratos', function ($table) {
            $table->dropColumn('activo');
        });
    }
}
