<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // Basic identification
            $table->integer('year');          // e.g. 2023
            $table->string('month');          // e.g. "January"

            // Sale details (from the 2023 Google Doc)
            $table->date('sale_date')->nullable();    // if you have exact dates
            $table->string('area')->nullable();       // Paphos, Limassol, etc.
            $table->string('agent')->nullable();      // Vita, Nicole, etc.
            $table->string('buyer_country')->nullable(); // UK, Cyprus, etc.

            // Financials
            $table->decimal('price', 15, 2)->nullable();       // property price
            $table->decimal('commission', 15, 2)->nullable();  // commission earned

            // Optional: source / notes if needed later
            $table->string('source')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
