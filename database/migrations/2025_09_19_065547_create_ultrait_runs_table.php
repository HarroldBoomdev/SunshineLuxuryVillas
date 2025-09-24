<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ultrait_runs', function (Blueprint $t) {
            $t->id();
            $t->string('source', 80)->index();                 // e.g. xml:full, xml:delta
            $t->boolean('dry_run')->default(true)->index();
            $t->timestamp('started_at')->useCurrent();
            $t->timestamp('finished_at')->nullable();
            $t->enum('status', ['queued','running','success','partial','failed'])->default('queued')->index();
            $t->unsignedInteger('new_count')->default(0);
            $t->unsignedInteger('update_count')->default(0);
            $t->unsignedInteger('image_count')->default(0);
            $t->json('notes')->nullable();
            $t->text('error')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ultrait_runs');
    }
};
