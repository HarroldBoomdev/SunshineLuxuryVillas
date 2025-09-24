<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingFieldsToPropertiesTable extends Migration
{
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $existing = Schema::getColumnListing('properties');

            if (!in_array('year_construction', $existing)) $table->string('year_construction')->nullable();
            if (!in_array('year_renovation', $existing)) $table->string('year_renovation')->nullable();
            if (!in_array('furnished', $existing)) $table->string('furnished')->nullable();
            if (!in_array('reference', $existing)) $table->string('reference')->nullable();
            if (!in_array('labels', $existing)) $table->text('labels')->nullable();
            if (!in_array('status', $existing)) $table->string('status')->nullable();
            if (!in_array('basement', $existing)) $table->string('basement')->nullable();
            if (!in_array('orientation', $existing)) $table->string('orientation')->nullable();
            if (!in_array('energyEfficiency', $existing)) $table->string('energyEfficiency')->nullable();
            if (!in_array('vat', $existing)) $table->string('vat')->nullable();
            if (!in_array('price', $existing)) $table->decimal('price', 15, 2)->nullable();
            if (!in_array('covered', $existing)) $table->string('covered')->nullable();
            if (!in_array('attic', $existing)) $table->string('attic')->nullable();
            if (!in_array('coveredVeranda', $existing)) $table->string('coveredVeranda')->nullable();
            if (!in_array('coveredParking', $existing)) $table->string('coveredParking')->nullable();
            if (!in_array('courtyard', $existing)) $table->string('courtyard')->nullable();
            if (!in_array('plot', $existing)) $table->string('plot')->nullable();
            if (!in_array('roofGarden', $existing)) $table->string('roofGarden')->nullable();
            if (!in_array('uncoveredVeranda', $existing)) $table->string('uncoveredVeranda')->nullable();
            if (!in_array('garden', $existing)) $table->string('garden')->nullable();
            if (!in_array('owner', $existing)) $table->string('owner')->nullable();
            if (!in_array('refId', $existing)) $table->string('refId')->nullable();
            if (!in_array('region', $existing)) $table->string('region')->nullable();
            if (!in_array('town', $existing)) $table->string('town')->nullable();
            if (!in_array('address', $existing)) $table->string('address')->nullable();
            if (!in_array('image_order', $existing)) $table->text('image_order')->nullable();
            if (!in_array('photos', $existing)) $table->text('photos')->nullable();
        });
    }

    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'year_construction',
                'year_renovation',
                'furnished',
                'reference',
                'labels',
                'status',
                'basement',
                'orientation',
                'energyEfficiency',
                'vat',
                'price',
                'covered',
                'attic',
                'coveredVeranda',
                'coveredParking',
                'courtyard',
                'plot',
                'roofGarden',
                'uncoveredVeranda',
                'garden',
                'owner',
                'refId',
                'region',
                'town',
                'address',
                'image_order',
                'photos',
            ]);
        });
    }
}
