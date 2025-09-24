<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ultrait_listing_texts', function (Blueprint $t) {
            $t->id();
            $t->string('external_id', 128)->index();     // link via XML <id>
            $t->string('lang', 8)->default('en')->index(); // e.g., en, es, fr
            $t->enum('kind', ['desc','title','features'])->default('desc')->index();
            $t->longText('text')->nullable();
            $t->timestamps();
            $t->unique(['external_id','lang','kind']);   // one row per kind/lang
        });
    }
    public function down(): void {
        Schema::dropIfExists('ultrait_listing_texts');
    }
};
