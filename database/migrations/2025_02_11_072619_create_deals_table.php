<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('client_name');
            $table->decimal('amount', 10, 2);
            $table->string('pipeline');
            $table->string('stage');
            $table->string('branch')->nullable();
            $table->string('assigned_to');
            $table->date('expected_close_date')->nullable();
            $table->timestamps();
        });
    }
        
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
