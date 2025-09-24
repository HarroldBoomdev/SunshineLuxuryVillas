<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('id_card_number')->nullable();
            $table->json('labels')->nullable();
            $table->string('referral_agent_contact')->nullable();
            $table->string('preferred_language')->nullable();
            $table->string('managing_agent')->nullable();
            $table->date('dob')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('region')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('nationality')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('referral_agent')->nullable();
            $table->string('lead_source')->nullable();
            $table->string('branch')->nullable();
            $table->string('subscription_status')->nullable();
            $table->text('notes')->nullable();
            $table->string('buyer_profile')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->string('pool')->nullable();
            $table->string('specifications')->nullable();
            $table->string('orientation')->nullable();
            $table->string('covered_area')->nullable();
            $table->integer('construction_year')->nullable();
            $table->string('floor')->nullable();
            $table->integer('purchase_budget')->nullable();
            $table->string('furnished')->nullable();
            $table->string('plot_area')->nullable();
            $table->string('parking')->nullable();
            $table->string('reasons_for_buying')->nullable();
            $table->string('time_frame')->nullable();
            $table->boolean('matching_system')->default(false);
            $table->json('contacts')->nullable();
            $table->boolean('notifications')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
};