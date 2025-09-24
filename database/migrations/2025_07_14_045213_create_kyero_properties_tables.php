<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create properties table
        if (!Schema::hasTable('properties')) {
            Schema::create('properties', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('external_id')->unique();
                $table->string('reference')->nullable();
                $table->decimal('price', 12, 2)->nullable();
                $table->string('property_type')->nullable();
                $table->string('town')->nullable();
                $table->string('province')->nullable();
                $table->float('latitude')->nullable();
                $table->float('longitude')->nullable();
                $table->integer('beds')->nullable();
                $table->integer('baths')->nullable();
                $table->boolean('pool')->default(false);
                $table->integer('built_area')->nullable();
                $table->integer('plot_area')->nullable();
                $table->text('description')->nullable();
                $table->text('external_url')->nullable();
                $table->timestamps();
            });
        }

        // Create property_images table
        if (!Schema::hasTable('property_images')) {
            Schema::create('property_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained()->onDelete('cascade');
                $table->text('image_url');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('property_images');
        Schema::dropIfExists('properties');
    }
};
