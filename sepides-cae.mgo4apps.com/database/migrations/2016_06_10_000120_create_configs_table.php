<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigsTable extends Migration
{
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->increments('id');
            // Nombre de la empresa corto que aparecerá en pantalla, emails, etc
            $table->string('nombre_corto', 10)->default('DEMO');
            // Lista separada por comas jpg,png,doc,xls
            $table->string('mimes_permitidos')->default('pdf,doc,docx');
            // Determina si se mandan invitaciones a los nuevos subcontratistas
            // para que éstos accedan y participen del CAE
            $table->boolean('invitar_subcontratistas')->default(true);
            // Días para emitir aviso por caducidad de documentos
            $table->unsignedInteger('caducidad_m_dias')->default(5);
            $table->unsignedInteger('caducidad_t_dias')->default(20);
            $table->unsignedInteger('caducidad_s_dias')->default(20);
            $table->unsignedInteger('caducidad_a_dias')->default(30);
            $table->unsignedInteger('caducidad_v_dias')->default(30);
            // Número de filas en tablas por defecto
            $table->unsignedInteger('filas_tablas')->default(25);
            $table->unsignedInteger('filas_tablas_modal')->default(10);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('configs');
    }
}
