<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ultrait_properties_raw', function (Blueprint $t) {
            $t->id();
            $t->foreignId('run_id')->constrained('ultrait_runs')->cascadeOnDelete();
            $t->string('external_id', 128)->index();            // <id> from XML
            $t->string('reference_code', 128)->nullable();      // <ref>
            $t->timestamp('xml_date')->nullable();              // <date>
            $t->timestamp('xml_date_updated')->nullable();      // if present
            $t->string('hash', 64)->index();                    // sha256 of raw property fragment
            $t->longText('property_xml_gzip_b64');              // base64(gzipped <property>...</property>)
            $t->json('meta')->nullable();                       // any quick metadata (counts, lang list, etc.)
            $t->timestamps();
            $t->unique(['external_id','hash']);                 // same id+content won’t be stored twice
        });
    }
    public function down(): void {
        Schema::dropIfExists('ultrait_properties_raw');
    }
};
