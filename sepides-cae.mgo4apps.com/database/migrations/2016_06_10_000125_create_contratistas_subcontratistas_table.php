<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContratistasSubcontratistasTable extends Migration
{
    public function up()
    {
        Schema::create('contratistas_subcontratistas', function (Blueprint $table) {
            $table->unsignedInteger('contratista_id');
            $table->unsignedInteger('subcontratista_id');

            $table->unique(['contratista_id', 'subcontratista_id'], 'pk');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contratistas_subcontratistas');
    }
}
