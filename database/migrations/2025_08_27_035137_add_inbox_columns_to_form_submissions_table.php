<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            // Add only if missing (safe re-run)
            if (!Schema::hasColumn('form_submissions', 'form_key')) {
                $table->string('form_key')->index()->after('id');
            }
            if (!Schema::hasColumn('form_submissions', 'name')) {
                $table->string('name')->nullable()->after('form_key');
            }
            if (!Schema::hasColumn('form_submissions', 'email')) {
                $table->string('email')->nullable()->after('name');
            }
            if (!Schema::hasColumn('form_submissions', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('form_submissions', 'reference')) {
                $table->string('reference')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('form_submissions', 'payload')) {
                $table->json('payload')->nullable()->after('reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('form_submissions', 'payload')) {
                $table->dropColumn('payload');
            }
            if (Schema::hasColumn('form_submissions', 'reference')) {
                $table->dropColumn('reference');
            }
            if (Schema::hasColumn('form_submissions', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('form_submissions', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('form_submissions', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('form_submissions', 'form_key')) {
                $table->dropColumn('form_key');
            }
        });
    }
};
