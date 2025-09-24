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
        Schema::create('diary_entries', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('type'); // e.g., Phone Call, Email
            $table->date('date'); // Date of the diary entry
            $table->time('time'); // Time of the diary entry
            $table->string('duration')->nullable(); // Duration (e.g., 0h 15m)
            $table->string('participants')->nullable(); // Names of participants
            $table->string('lead_source')->nullable(); // Lead source
            $table->text('notes')->nullable(); // Notes field
            $table->string('linked_to')->nullable(); // Linked record
            $table->string('color')->nullable(); // Color code (e.g., #ff0000)
            $table->boolean('is_done')->default(false); // Mark as done
            $table->timestamps(); // Laravel's created_at and updated_at
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diary_entries');
    }
};
