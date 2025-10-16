<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('property_status', 20)->default('')->index(); // '' = None, 'Active' = publish
            // optional: last sync tracking
            $table->timestamp('published_at')->nullable();
            $table->string('external_slug')->nullable(); // id/slug returned by frontend API
        });
    }
    public function down(): void {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['property_status','published_at','external_slug']);
        });
    }
};
