<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ultrait_listing_media', function (Blueprint $t) {
            $t->id();
            $t->string('external_id', 128)->index();     // link via XML <id>
            $t->enum('kind', ['image','floorplan','video','tour'])->default('image')->index();
            $t->text('src_url');                         // original URL from XML
            $t->unsignedInteger('ordinal')->default(0)->index();
            $t->string('caption', 512)->nullable();
            $t->string('s3_key', 512)->nullable()->index(); // filled after upload step
            $t->enum('status', ['queued','downloaded','uploaded','failed'])->default('queued')->index();
            $t->text('error')->nullable();
            $t->timestamps();
            $t->index(['external_id','kind','ordinal']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('ultrait_listing_media');
    }
};
