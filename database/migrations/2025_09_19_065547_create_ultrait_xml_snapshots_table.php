<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ultrait_xml_snapshots', function (Blueprint $t) {
            $t->id();
            $t->foreignId('run_id')->constrained('ultrait_runs')->cascadeOnDelete();
            $t->string('feed_url', 1024);
            $t->unsignedBigInteger('content_length')->nullable();
            $t->string('content_type', 128)->nullable();
            $t->string('content_encoding', 32)->nullable();     // e.g. gzip
            $t->longText('body_gzip_b64');                       // base64(gzipped feed)
            $t->timestamp('fetched_at')->useCurrent();
            $t->timestamps();
            $t->index(['run_id','fetched_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('ultrait_xml_snapshots');
    }
};
