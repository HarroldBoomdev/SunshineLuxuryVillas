<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('category')->nullable();
            $table->string('branch')->nullable();
            $table->string('bundle')->nullable();
            $table->string('keysafe')->nullable();
            $table->string('proptype')->nullable();
            $table->string('floor')->nullable();
            $table->integer('parkingSpaces')->nullable();
            $table->integer('kitchens')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('toilets')->nullable();
            $table->string('furnished')->nullable();
            $table->string('orientation')->nullable();
            $table->integer('yearRenovation')->nullable();
            $table->date('movedinReady')->nullable(); 
            $table->decimal('valByAgent', 10, 2)->nullable(); 
            $table->decimal('price', 10, 2)->nullable();
            $table->string('vat')->nullable();
            $table->decimal('groundRent', 10, 2)->nullable();
            $table->string('referrence')->nullable(); 
            $table->string('managing_agent')->nullable(); 
            $table->integer('number')->nullable(); 
            $table->json('labels')->nullable();
            $table->string('status')->nullable();
            $table->integer('floors')->nullable();
            $table->integer('livingRooms')->nullable(); 
            $table->integer('bedrooms')->nullable();
            $table->integer('showers')->nullable();
            $table->integer('basement')->nullable();
            $table->integer('yearConstruction')->nullable(); 
            $table->string('energyEfficiency')->nullable(); 
            $table->decimal('communalCharge', 10, 2)->nullable(); 
            $table->string('comChargeFreq')->nullable(); 
            $table->decimal('reducedPrice', 10, 2)->nullable(); 
            $table->decimal('commission', 10, 2)->nullable();
            $table->string('owner')->nullable();
            $table->string('refId')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('accuracy')->nullable();
            $table->string('street')->nullable();
            $table->string('complex')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('zipcode')->nullable();
            $table->decimal('covered', 8, 2)->nullable();
            $table->decimal('attic', 8, 2)->nullable();
            $table->decimal('coveredVeranda', 8, 2)->nullable(); 
            $table->decimal('coveredParking', 8, 2)->nullable(); 
            $table->decimal('courtyard', 8, 2)->nullable();
            $table->decimal('roofGarden', 8, 2)->nullable(); 
            $table->decimal('uncoveredVeranda', 8, 2)->nullable(); 
            $table->decimal('plot', 8, 2)->nullable();
            $table->decimal('garden', 8, 2)->nullable();
            $table->string('regnum')->nullable();
            $table->string('plotnum')->nullable();
            $table->string('titleDead')->nullable(); 
            $table->string('section')->nullable();
            $table->string('sheetPlan')->nullable(); 
            $table->decimal('share', 8, 2)->nullable();
            $table->decimal('amenities', 8, 2)->nullable();
            $table->decimal('sea', 8, 2)->nullable();
            $table->decimal('schools', 8, 2)->nullable();
            $table->decimal('airport', 8, 2)->nullable();
            $table->decimal('publicTransport', 8, 2)->nullable(); 
            $table->decimal('resort', 8, 2)->nullable();
            $table->json('facilities')->nullable();
            $table->text('photos')->nullable();
            $table->text('floor_plans')->nullable();
            $table->text('titledeed')->nullable();
            $table->string('kuula_link')->nullable();
            $table->json('youtube_links')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
