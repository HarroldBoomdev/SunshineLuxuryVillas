<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogsTable extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('trace_id')->unique();
            $table->string('type'); // CREATE, UPDATE, DELETE, SYSTEM
            $table->string('resource_action'); // E.g., "User signed in"
            $table->string('user_name')->nullable(); // Name of the user performing the action
            $table->string('user_image')->nullable(); // Avatar or profile image
            $table->ipAddress('ip_address')->nullable();
            $table->timestamp('date_time'); // When the event occurred
            $table->timestamps(); // Laravel's created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
}
