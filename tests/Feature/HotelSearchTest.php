<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HotelSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_hotels_with_swimming_pool()
    {
        // 1. Create a location
        $location = DB::table('Location')->insertGetId([
            'Name' => 'Test Location',
            'slugid' => '12345',
            'Slug' => 'test-location',
            'LocationId' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create an amenity
        $amenity = DB::table('TPHotel_amenities')->insertGetId([
            'name' => 'Swimming pool',
            'slug' => 'swimming-pool',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Create a hotel
        $hotel = DB::table('TPHotel')->insertGetId([
            'name' => 'Test Hotel',
            'slugid' => '12345',
            'slug' => 'test-hotel',
            'hotelid' => 1,
            'location_id' => 1,
            'facilities' => (string)$amenity,
            'OverviewShortDesc' => 'Test description',
            'stars' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Call the endpoint
        $response = $this->get('/ho-12345-test-location');

        // 5. Assert that the response is successful
        $response->assertStatus(200);

        // 6. Assert that the response contains the test hotel
        $response->assertSee('Test Hotel');
    }
}
