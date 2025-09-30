<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('featured_properties', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // property reference code
            $table->unsignedTinyInteger('position')->default(0); // order 0–11
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('featured_properties');
    }
};
