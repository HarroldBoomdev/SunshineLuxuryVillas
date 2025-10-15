<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $t) {
            // Areas (m²)
            if (!Schema::hasColumn('properties','covered_m2'))          $t->decimal('covered_m2', 10, 2)->nullable()->after('price');
            if (!Schema::hasColumn('properties','plot_m2'))             $t->decimal('plot_m2', 10, 2)->nullable()->after('covered_m2');
            if (!Schema::hasColumn('properties','roofgarden_m2'))       $t->decimal('roofgarden_m2', 10, 2)->nullable()->after('plot_m2');
            if (!Schema::hasColumn('properties','attic_m2'))            $t->decimal('attic_m2', 10, 2)->nullable()->after('roofgarden_m2');
            if (!Schema::hasColumn('properties','covered_veranda_m2'))  $t->decimal('covered_veranda_m2', 10, 2)->nullable()->after('attic_m2');
            if (!Schema::hasColumn('properties','uncovered_veranda_m2'))$t->decimal('uncovered_veranda_m2', 10, 2)->nullable()->after('covered_veranda_m2');
            if (!Schema::hasColumn('properties','garden_m2'))           $t->decimal('garden_m2', 10, 2)->nullable()->after('uncovered_veranda_m2');
            if (!Schema::hasColumn('properties','basement_m2'))         $t->decimal('basement_m2', 10, 2)->nullable()->after('garden_m2');
            if (!Schema::hasColumn('properties','courtyard_m2'))        $t->decimal('courtyard_m2', 10, 2)->nullable()->after('basement_m2');
            if (!Schema::hasColumn('properties','covered_parking_m2'))  $t->decimal('covered_parking_m2', 10, 2)->nullable()->after('courtyard_m2');

            // Distances (km)
            if (!Schema::hasColumn('properties','amenities_km'))        $t->decimal('amenities_km', 10, 2)->nullable()->after('covered_parking_m2');
            if (!Schema::hasColumn('properties','airport_km'))          $t->decimal('airport_km', 10, 2)->nullable()->after('amenities_km');
            if (!Schema::hasColumn('properties','sea_km'))              $t->decimal('sea_km', 10, 2)->nullable()->after('airport_km');
            if (!Schema::hasColumn('properties','public_transport_km')) $t->decimal('public_transport_km', 10, 2)->nullable()->after('sea_km');
            if (!Schema::hasColumn('properties','schools_km'))          $t->decimal('schools_km', 10, 2)->nullable()->after('public_transport_km');
            if (!Schema::hasColumn('properties','resort_km'))           $t->decimal('resort_km', 10, 2)->nullable()->after('schools_km');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $t) {
            $cols = [
                'covered_m2','plot_m2','roofgarden_m2','attic_m2',
                'covered_veranda_m2','uncovered_veranda_m2','garden_m2',
                'basement_m2','courtyard_m2','covered_parking_m2',
                'amenities_km','airport_km','sea_km','public_transport_km','schools_km','resort_km',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('properties', $c)) $t->dropColumn($c);
            }
        });
    }
};

