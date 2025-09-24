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
        Schema::create('sale_properties', function (Blueprint $table) {
            $table->id();
            $table->string('property_id');
            $table->string('reference')->nullable();
            $table->string('location')->nullable();
            $table->string('property_type')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->string('price')->nullable();
            $table->boolean('pool')->default(0);
            $table->boolean('featured')->default(0);
            $table->boolean('live')->default(0);
            $table->string('preview_image')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_properties');
    }
};
