<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveFieldRolFromUser extends Migration
{
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('rol');
        });
    }

    public function down()
    {
        Schema::table('users', function ($table) {
            $table->string('rol', 3)->default('CTA');
        });
    }
}
