<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            // Basics
            $table->boolean('is_poa')->default(false)->after('price');

            // Property details
            $table->string('long_let', 10)->nullable()->after('titleDead');
            $table->string('leasehold', 10)->nullable()->after('long_let');
            $table->decimal('terrace_m2', 10, 2)->nullable()->after('roofgarden_m2');
            $table->text('pool_description')->nullable()->after('pool');

            // Price / currency
            $table->string('currency', 3)->nullable()->after('price');
            $table->boolean('is_poa_current')->default(false)->after('is_poa');
            $table->decimal('reduction_percent', 5, 2)->nullable()->after('reducedPrice');
            $table->decimal('reduction_value', 10, 2)->nullable()->after('reduction_percent');
            $table->boolean('display_reduction_as_percent')->default(false)->after('reduction_value');
            $table->decimal('monthly_rent', 10, 2)->nullable()->after('commission');

            // Specifics
            $table->string('listing_type')->nullable()->after('status');
            $table->string('plan_zone', 50)->nullable()->after('listing_type');
            $table->string('sea_view', 10)->nullable()->after('plan_zone');
            $table->string('for_sale_board', 10)->nullable()->after('sea_view');
            $table->integer('property_age')->nullable()->after('for_sale_board');
            $table->text('plot_description')->nullable()->after('plot_m2');
        });
    }

    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'is_poa',
                'long_let',
                'leasehold',
                'terrace_m2',
                'pool_description',
                'currency',
                'is_poa_current',
                'reduction_percent',
                'reduction_value',
                'display_reduction_as_percent',
                'monthly_rent',
                'listing_type',
                'plan_zone',
                'sea_view',
                'for_sale_board',
                'property_age',
                'plot_description',
            ]);
        });
    }

};
