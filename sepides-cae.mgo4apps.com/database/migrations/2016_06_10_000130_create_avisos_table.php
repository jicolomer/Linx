<?php


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreateAvisosTable extends Migration
{

    public function up()
    {
        Schema::create('avisos', function (Blueprint $table) {
            $table->increments('id');
            $table->text('texto');
            $table->string('url', 255);
            $table->timestamps();
        });

        Schema::create('avisos_users', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('aviso_id');
            $table->boolean('mostrado');
            $table->boolean('leido');

            $table->unique(['user_id', 'aviso_id'], 'pk');
        });
    }


    public function down()
    {
        Schema::dropIfExists('avisos');
        Schema::dropIfExists('avisos_users');
    }

}
