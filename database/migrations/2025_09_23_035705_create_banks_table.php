<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('address')->nullable();
            $table->string('name');         // dropdown uses this
            $table->string('telephone')->nullable();
            $table->string('mobile')->nullable();
            $table->timestamps();
        });

        // keep/restore the linking table so the dropdown can filter properties
        Schema::create('bank_property', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('banks')->cascadeOnDelete();
            $table->string('property_reference')->index();
            $table->timestamps();
            $table->unique(['bank_id','property_reference']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('bank_property');
        Schema::dropIfExists('banks');
    }
};
