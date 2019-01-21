<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('empresa_id')->default(0);
            $table->string('nombre', 100);
            $table->string('nif', 9);
            $table->string('telefono', 9);
            $table->string('email')->unique();
            $table->string('password');
            $table->string('cargo');
            $table->string('rol', 3)->default('CTA');
            $table->unsignedInteger('centro_id')->nullable(); // Para los de control de accesos
            $table->boolean('activo')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
