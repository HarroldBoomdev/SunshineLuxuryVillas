<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('region')->nullable();
            $table->string('town')->nullable();
            $table->string('address')->nullable();
        });
    }


    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['region', 'town', 'address']);
        });
    }

};
