<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ultrait_listings_staging', function (Blueprint $t) {
            $t->id();
            $t->string('external_id', 128)->unique();   // <id>
            $t->string('reference_code', 128)->nullable(); // <ref>
            $t->enum('status', ['sale','longlet','shortlet','unknown'])->default('unknown')->index(); // from <price_freq> or feed context
            $t->decimal('price', 14, 2)->nullable();
            $t->char('currency', 3)->nullable();
            $t->string('type', 64)->nullable();
            $t->unsignedTinyInteger('beds')->nullable();
            $t->unsignedTinyInteger('baths')->nullable();
            $t->unsignedInteger('built')->nullable();
            $t->unsignedInteger('plot')->nullable();
            $t->string('country', 64)->nullable();
            $t->string('province', 128)->nullable()->index();
            $t->string('town', 128)->nullable()->index();
            $t->decimal('latitude', 10, 7)->nullable();
            $t->decimal('longitude', 10, 7)->nullable();
            $t->text('detail_url')->nullable();
            $t->timestamp('xml_date')->nullable();
            $t->timestamp('xml_date_updated')->nullable();
            $t->string('digest', 64)->nullable()->index(); // SHA-256 of key fields
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ultrait_listings_staging');
    }
};
