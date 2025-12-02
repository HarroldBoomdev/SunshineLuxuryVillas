<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_summary', function (Blueprint $table) {
            $table->id();

            $table->integer('year');    // e.g. 2023

            // Yearly totals
            $table->integer('total_sales')->default(0);        // count of sales entries
            $table->decimal('total_value', 15, 2)->default(0); // sum of all prices
            $table->decimal('total_commission', 15, 2)->default(0);

            // useful aggregates
            $table->decimal('avg_price', 15, 2)->nullable();
            $table->decimal('avg_commission', 15, 2)->nullable();

            // example: leads → sales conversion rate
            $table->integer('total_leads')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0); // %

            $table->timestamps();

            $table->unique('year'); // one row per year
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_summary');
    }
};
