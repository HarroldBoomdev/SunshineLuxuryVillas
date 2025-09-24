<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewingsTable extends Migration
{
    public function up()
    {
        Schema::create('viewings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Reference to the user who created the viewing
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Reference to the agent assigned
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone');
            $table->text('client_whatsapp')->nullable(); // Optional WhatsApp field
            $table->foreignId('property_id')->constrained()->onDelete('cascade'); // Reference to the property being viewed
            $table->date('viewing_date');
            $table->time('viewing_time');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('viewings');
    }
}
