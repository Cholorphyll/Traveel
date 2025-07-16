<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use Illuminate\Support\Facades\DB;

class SitemapXmlController extends Controller
{
    
    public function sitemapindex(){        
        $recordsPerPage = 20000;

        $hotellistingcount = DB::table('Temp_Mapping')
            ->join('Location', 'Temp_Mapping.slugid', '=', 'Location.slugid')
            ->distinct()
            ->count('Temp_Mapping.slugid');
        $hotcount = ceil($hotellistingcount / $recordsPerPage);
		 
        $tphotelcount = DB::table('TPHotel as h')
            ->join('Location as l','h.slugid','=','l.slugid')
            ->count();
        $tpHdetailcount = ceil($tphotelcount / $recordsPerPage);

        // Count amenities landing pages
        $amenitiesTotal = DB::table('Tripadvisor_amenities_url as ta')
            ->join('Location as l', 'l.LocationId', '=', 'ta.LocationId')
            ->join('TPHotel_amenities as tpha', 'tpha.name', '=', 'ta.Keyword')
            ->whereNotNull('tpha.slug')
            ->where('tpha.slug', '!=', '')
            ->count();
        
        // Count locations × neighborhoods
        $neighborhoodsTotal = DB::table('Location as l')
            ->join('Neighborhood as n', 'n.LocationId', '=', 'l.LocationId')
            ->whereNotNull('n.Latitude')
            ->where('n.Latitude', '!=', '')
            ->count();
        
        // Count locations × sights
        $sightsTotal = DB::table('Location as l')
            ->join('Sight as s', 's.LocationId', '=', 'l.LocationId')
            ->whereNotNull('s.Latitude')
            ->where('s.Latitude', '!=', '')
            ->count();
        
        // Total landing pages count
        $landingPagesCount = $amenitiesTotal + $neighborhoodsTotal + $sightsTotal;
        $landingPagesPageCount = ceil($landingPagesCount /$recordsPerPage);
		 
        $Sight_totalRecords = DB::table('Sight')->count();
        $Sightcount = ceil($Sight_totalRecords / $recordsPerPage);
      //  $location_total = DB::table('Location')->count();
		 $location_total =  DB::table('Location as l')
		->join('Sight as s', 'l.LocationId', '=', 's.LocationId')		
		->distinct()
		->count('l.LocationId');
		
        $locationcount = ceil($location_total  / $recordsPerPage);

        return response()->view('sitemap.sitemapindex',['hotcount'=>$hotcount,'locationcount'=>$locationcount,'landingPagesPageCount' => $landingPagesPageCount,'tpHdetailcount'=>$tpHdetailcount])->header('Content-Type', 'text/xml');
    }

    public function sitemaping(){
       
        $hotellistingcount = DB::table('TPLocations')->count();
        $hotcount = ceil($hotellistingcount / $recordsPerPage);
        $tphotelcount = DB::table('TPHotel')->count();
        $tpHdetailcount = ceil($tphotelcount / $recordsPerPage);
        $locationcount = ceil($location_total  / $recordsPerPage);
     
	
        return response()->view('sitemap.sitemaping',['hotcount'=>$hotcount,'tpHdetailcount'=>$tpHdetailcount]);     
    }

    public function generateLandingPagesSitemap($page)
    {
        $limit = 20000;
        $offset = ($page - 1) * $limit;

        // Total counts for each category
        $amenitiesTotal = DB::table('Tripadvisor_amenities_url as ta')
            ->join('Location as l', 'l.LocationId', '=', 'ta.LocationId')
            ->join('TPHotel_amenities as tpha', 'tpha.name', '=', 'ta.Keyword')
            ->whereNotNull('tpha.slug')->where('tpha.slug', '!=', '')->count();

        $neighborhoodsTotal = DB::table('Location as l')
            ->join('Neighborhood as n', 'n.LocationId', '=', 'l.LocationId')
            ->whereNotNull('n.Latitude')->where('n.Latitude', '!=', '')->count();

        $sightsTotal = DB::table('Location as l')
            ->join('Sight as s', 's.LocationId', '=', 'l.LocationId')
            ->whereNotNull('s.Latitude')->where('s.Latitude', '!=', '')->count();

        // Determine which category to fetch from based on the page number
        if ($offset < $amenitiesTotal) {
            $amenities = DB::table('Tripadvisor_amenities_url as ta')
                ->join('Location as l', 'l.LocationId', '=', 'ta.LocationId')
                ->join('TPHotel_amenities as tpha', 'tpha.name', '=', 'ta.Keyword')
                ->select(['ta.LocationId as Tid', 'l.slugid', 'l.Slug as main_slug', 'tpha.slug as secondary_slug', DB::raw("'hotel' as type")])
                ->whereNotNull('tpha.slug')->where('tpha.slug', '!=', '')
                ->skip($offset)->take($limit)->get();
            $Location = $amenities;
        } elseif ($offset < $amenitiesTotal + $neighborhoodsTotal) {
            $neighborhoodOffset = $offset - $amenitiesTotal;
            $neighborhoods = DB::table('Location as l')
                ->join('Neighborhood as n', 'n.LocationId', '=', 'l.LocationId')
                ->select(['l.LocationId as Tid', 'l.Slug as main_slug', 'l.slugid', 'n.slug as secondary_slug', DB::raw("'neighborhood' as type")])
                ->whereNotNull('n.Latitude')->where('n.Latitude', '!=', '')
                ->skip($neighborhoodOffset)->take($limit)->get();
            $Location = $neighborhoods;
        } else {
            $sightOffset = $offset - $amenitiesTotal - $neighborhoodsTotal;
            $sights = DB::table('Location as l')
                ->join('Sight as s', 's.LocationId', '=', 'l.LocationId')
                ->select(['l.LocationId as Tid', 'l.Slug as main_slug', 'l.slugid', 's.SightId as secondary_slug', DB::raw("'sight' as type")])
                ->whereNotNull('s.Latitude')->where('s.Latitude', '!=', '')
                ->skip($sightOffset)->take($limit)->get();
            $Location = $sights;
        }

        return response()->view('sitemap.landing-pages', ['Location' => $Location])
            ->header('Content-Type', 'application/xml');
    }

     public function hotellisting($pagenumber){
        $limit = 20000;
        $offset = ($pagenumber - 1) * $limit;    

        // Get locations that have at least one hotel with a valid slugid
        $Location = DB::table('Temp_Mapping as m')
            ->join('Location as l', 'l.LocationId', '=', 'm.Tid')
            ->select('m.Tid', 'm.slug', 'l.slugid')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('TPHotel')
                      ->whereRaw('TPHotel.LocationId = m.Tid')
                      ->whereNotNull('TPHotel.slugid')
                      ->where('TPHotel.slugid', '!=', '')
                      ->limit(1);
            })
            // Ensure slugid exists in Temp_Mapping and is valid
            ->whereNotNull('m.slugid')
            ->where('m.slugid', '!=', '')
            ->whereNotNull('l.slugid')
            ->where('l.slugid', '!=', '')
            ->skip($offset)
            ->distinct()
            ->limit($limit)
            ->orderby('m.Tid', 'asc')
            ->get();

        // If no locations were found, return an empty sitemap
        if ($Location->isEmpty()) {
            return response()
                ->view('sitemap.hotel_listing', ['Location' => collect()])
                ->header('Content-Type', 'text/xml');
        }

        return response()->view('sitemap.hotel_listing', [
            'Location' => $Location
        ])->header('Content-Type', 'text/xml');
    }

  public function hoteldetail($pagenumber){
        $limit = 20000;
        
        try {
            // Use keyset pagination instead of offset pagination for better performance
            if ($pagenumber > 1) {
                // Get the last ID from the previous page to use as a starting point
                $lastId = ($pagenumber - 1) * $limit;
                
                // Use a more efficient query with WHERE instead of OFFSET
                $Location = DB::table('TPHotel as h')
                    ->join('Location as l', 'h.slugid', '=', 'l.slugid')
                    ->select('h.id', 'h.slug', 'l.slugid')
                    ->where('h.id', '>', $lastId)
                    ->distinct()
                    ->limit($limit)
                    ->orderBy('h.id', 'asc')
                    ->get();
            } else {
                // First page - no need for offset
                $Location = DB::table('TPHotel as h')
                    ->join('Location as l', 'h.slugid', '=', 'l.slugid')
                    ->select('h.id', 'h.slug', 'l.slugid')
                    ->distinct()
                    ->limit($limit)
                    ->orderBy('h.id', 'asc')
                    ->get();
            }
            
            // Log query execution time for monitoring
            \Log::info('Hotel sitemap page ' . $pagenumber . ' generated with ' . count($Location) . ' items');
            
            return response()->view('sitemap.hotelDetail', [
                'Location' => $Location
            ])->header('Content-Type', 'text/xml');
        } catch (\Exception $e) {
            \Log::error('Error generating hotel sitemap page ' . $pagenumber . ': ' . $e->getMessage());
            return response()->view('sitemap.error', [
                'message' => 'An error occurred while generating the sitemap'
            ])->header('Content-Type', 'text/xml');
        }
    }
    public function checkExploreHotelCount() {
        $hotelCount = DB::table('TPHotel as h')    
            ->join('Location as l','h.slugid','=','l.slugid')
            ->distinct()
            ->count();
            
        $recordsPerPage = 20000;
        $totalPages = ceil($hotelCount / $recordsPerPage);
        
        return response()->json([
            'total_hotels' => $hotelCount,
            'urls_per_page' => $recordsPerPage,
            'total_pages' => $totalPages,
            'message' => "There are {$hotelCount} hotels in total for LocationId 854502, split across {$totalPages} sitemap files with {$recordsPerPage} URLs per page."
        ]);
    }

public function staticSitemap($pagenumber) {
    $limit = 20000; // Define the limit for the number of records per page
    $offset = ($pagenumber - 1) * $limit; // Calculate the offset based on the page number

    // Query the database for the URLs
    $urls = DB::table('static_sitemap')
        ->select('url') // Select the url column
        ->skip($offset) // Apply the offset
        ->limit($limit) // Limit the number of records
        ->orderBy('id', 'asc') // Order by the primary key
        ->get(); // Execute the query

    // Return the response with the view
    return response()->view('sitemap.static_pages', [
        'landing' => $urls // Pass the URLs to the view
    ])->header('Content-Type', 'text/xml'); // Set the content type for XML
}

public function location_listing($pagenumber){
    $limit = 20000;
    $offset = ($pagenumber - 1) * $limit;
   // $locations = DB::table('Location')->skip($offset)->limit($limit)->orderBy('LocationId','asc')->get();
    $locations = DB::table('Location as l')
    ->join('Sight as s', 'l.LocationId', '=', 's.LocationId')
    ->select('l.Slug', 'l.slugid', 'l.LocationId')
    ->distinct()
    ->skip($offset)
    ->limit($limit)
    ->orderBy('l.LocationId', 'asc')
    ->get();
    
    return response()->view('sitemap.index',['Location'=>$locations])->header('Content-Type','text/xml');
  }
}
