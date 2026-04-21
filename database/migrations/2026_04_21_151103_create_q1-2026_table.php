<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('q1-2026', function (Blueprint $table) {
            $table->id();
            $table->string('psgc_code')->unique()->index()->comment('PSGC code for the region this province belongs to');
            $table->string('name')->nullable()->comment('Name of the location');
            $table->string('correspondence_code')->nullable()->comment('Correspondence code for the location');
            $table->string('geographic_level')->nullable()->comment('Geographic level of the location (e.g., region, province, city, barangay)');
            $table->string('old_name')->nullable()->comment('Old name of the location, if applicable');
            $table->string('city_classification')->nullable()->comment('City classification (e.g., highly urbanized, independent component, component city)');
            $table->string('income_classification')->nullable()->comment('Income classification (e.g., 1st class, 2nd class, etc.)');
            $table->string('urban_rural')->nullable()->comment('Urban or rural classification');
            $table->string('population')->nullable()->comment('Population of the location');
            $table->string('region_code')->nullable()->comment('Region code for the location');
            $table->string('province_code')->nullable()->comment('Province code for the location');
            $table->string('mun_city_code')->nullable()->comment('Municipality or city code for the location');
            $table->string('barangay_code')->nullable()->comment('Barangay code for the location');
            $table->string('mun_city_identifier')->nullable()->comment('Identifier for municipalities or cities, if applicable');
            $table->string('barangay_identifier')->nullable()->comment('Identifier for barangays, if applicable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('q1-2026');
    }
};
