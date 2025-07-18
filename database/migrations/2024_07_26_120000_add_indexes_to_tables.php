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
        Schema::table('TPHotel', function (Blueprint $table) {
            $table->index('slugid');
            $table->index('stars');
            $table->index('rating');
            $table->index('pricefrom');
            $table->index('LocationId');
            $table->index('propertyType');
            $table->index('facilities');
        });

        Schema::table('Location', function (Blueprint $table) {
            $table->index('slugid');
            $table->index('ParentId');
        });

        Schema::table('Neighborhood', function (Blueprint $table) {
            $table->index('LocationID');
        });

        Schema::table('Sight', function (Blueprint $table) {
            $table->index('Location_id');
            $table->index('Avg_MonthlySearches');
        });

        Schema::table('Temp_Mapping', function (Blueprint $table) {
            $table->index('slugid');
            $table->index('Tid');
            $table->index('LocationId');
        });

        Schema::table('TPHotel_amenities', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('TPRoomtype_tmp', function (Blueprint $table) {
            $table->index('hotelid');
            $table->index('refundable');
            $table->index('breakfast');
        });

        Schema::table('TPRoomtype', function (Blueprint $table) {
            $table->index('hotelid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('TPHotel', function (Blueprint $table) {
            $table->dropIndex(['slugid']);
            $table->dropIndex(['stars']);
            $table->dropIndex(['rating']);
            $table->dropIndex(['pricefrom']);
            $table->dropIndex(['LocationId']);
            $table->dropIndex(['propertyType']);
            $table->dropIndex(['facilities']);
        });

        Schema::table('Location', function (Blueprint $table) {
            $table->dropIndex(['slugid']);
            $table->dropIndex(['ParentId']);
        });

        Schema::table('Neighborhood', function (Blueprint $table) {
            $table->dropIndex(['LocationID']);
        });

        Schema::table('Sight', function (Blueprint $table) {
            $table->dropIndex(['Location_id']);
            $table->dropIndex(['Avg_MonthlySearches']);
        });

        Schema::table('Temp_Mapping', function (Blueprint $table) {
            $table->dropIndex(['slugid']);
            $table->dropIndex(['Tid']);
            $table->dropIndex(['LocationId']);
        });

        Schema::table('TPHotel_amenities', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('TPRoomtype_tmp', function (Blueprint $table) {
            $table->dropIndex(['hotelid']);
            $table->dropIndex(['refundable']);
            $table->dropIndex(['breakfast']);
        });

        Schema::table('TPRoomtype', function (Blueprint $table) {
            $table->dropIndex(['hotelid']);
        });
    }
};
