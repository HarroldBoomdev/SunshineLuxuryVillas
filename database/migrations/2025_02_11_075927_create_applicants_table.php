<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('Title')->nullable();
            $table->string('FirstName');
            $table->string('LastName')->nullable();
            $table->string('TelephoneEvening')->nullable();
            $table->string('TelephoneDay')->nullable();
            $table->string('Mobile')->nullable();
            $table->string('Mobile2')->nullable();
            $table->string('Email')->unique();
            $table->string('Email2')->nullable();
            $table->text('Address')->nullable();
            $table->integer('MinimumPrice')->nullable();
            $table->integer('MaximumPrice')->nullable();
            $table->integer('MinimumBedrooms')->nullable();
            $table->boolean('ReceiveUpdates')->default(false);
            $table->string('Status')->nullable();
            $table->text('Notes')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
