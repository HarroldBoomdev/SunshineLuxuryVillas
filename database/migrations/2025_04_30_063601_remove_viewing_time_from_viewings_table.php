<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('viewings', function (Blueprint $table) {
            $table->dropColumn('viewing_time');
        });
    }

    public function down()
    {
        Schema::table('viewings', function (Blueprint $table) {
            $table->time('viewing_time')->nullable();
        });
    }

};
