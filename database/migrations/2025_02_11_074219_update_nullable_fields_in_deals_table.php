<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->string('pipeline')->nullable()->change();
            $table->string('branch')->nullable()->change();
            $table->string('assigned_to')->nullable()->change();
            $table->date('expected_close_date')->nullable()->change();
        });
    }

    public function down()
    {
        
    }

};
