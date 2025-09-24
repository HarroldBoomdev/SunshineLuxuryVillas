<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('viewings', function (Blueprint $table) {
            if (!Schema::hasColumn('viewings', 'title')) {
                $table->string('title')->nullable()->after('assigned_to');
            }
            if (!Schema::hasColumn('viewings', 'activity_type')) {
                $table->string('activity_type')->nullable()->after('title');
            }
            if (!Schema::hasColumn('viewings', 'duration')) {
                $table->string('duration')->nullable()->after('viewing_date');
            }
            if (!Schema::hasColumn('viewings', 'notes')) {
                $table->text('notes')->nullable()->after('duration');
            }
            if (!Schema::hasColumn('viewings', 'linked_to')) {
                $table->string('linked_to')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('viewings', 'is_done')) {
                $table->boolean('is_done')->default(false)->after('linked_to');
            }
        });
    }

    public function down()
    {
        Schema::table('viewings', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'activity_type',
                'duration',
                'notes',
                'linked_to',
                'is_done',
            ]);
        });
    }

};
