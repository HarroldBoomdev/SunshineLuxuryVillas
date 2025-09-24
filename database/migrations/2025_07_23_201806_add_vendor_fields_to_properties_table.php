<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorFieldsToPropertiesTable extends Migration
{
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'price_freq')) $table->string('price_freq')->nullable();
            if (!Schema::hasColumn('properties', 'vendor_name')) $table->string('vendor_name')->nullable();
            if (!Schema::hasColumn('properties', 'telephone')) $table->string('telephone')->nullable();
            if (!Schema::hasColumn('properties', 'mobile')) $table->string('mobile')->nullable();
            if (!Schema::hasColumn('properties', 'title')) $table->string('title')->nullable();
            if (!Schema::hasColumn('properties', 'location')) $table->string('location')->nullable();
            if (!Schema::hasColumn('properties', 'pool')) $table->string('pool')->nullable();
            if (!Schema::hasColumn('properties', 'property_description_alt')) $table->text('property_description_alt')->nullable();
            if (!Schema::hasColumn('properties', 'features')) $table->longText('features')->nullable();
        });
    }

    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'price_freq',
                'vendor_name',
                'telephone',
                'mobile',
                'title',
                'location',
                'pool',
                'property_description_alt',
                'features',
            ]);
        });
    }
}
