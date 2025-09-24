<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessLogsTable extends Migration
{
    public function up()
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('trace_id')->unique();
            $table->string('type'); 
            $table->string('url');
            $table->string('resource')->nullable(); 
            $table->string('user_name');
            $table->string('user_image')->nullable(); 
            $table->ipAddress('ip_address');
            $table->string('operating_system')->nullable();
            $table->timestamp('date_time');
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('access_logs');
    }
}
