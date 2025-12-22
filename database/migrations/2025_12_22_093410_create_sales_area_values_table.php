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
        Schema::create('sales_area_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('area', 50);
            $table->decimal('total_value', 14, 2)->default(0);
            $table->decimal('commission', 14, 2)->nullable();
            $table->timestamps();

            $table->unique(['year', 'area']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_area_values');
    }
};
