<?php

use App\Jobs\ImportDataJob;
use Illuminate\Http\Request;
use App\Jobs\ImportSightsJob;
use App\Jobs\ImportExperienceJob;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\sendEmail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Business_backend;
use App\Http\Controllers\HotelsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\url\UrlController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExperinceController;
use App\Http\Controllers\HOSegmentController;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SitemapXmlController;

use App\Http\Controllers\UrlContentController;
use App\Http\Controllers\UrlListingController;
use App\Http\Controllers\HotelDetailController;
use App\Http\Controllers\LandingPageController;

use App\Http\Controllers\LocationSeoController;
use App\Http\Controllers\StaticPagesController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\Frontend\HotelController;
use App\Http\Controllers\Frontend\SigninController;
use App\Http\Controllers\Frontend\WeatherController;
use App\Http\Controllers\Frontend\BusinessController;
use App\Http\Controllers\HotelFilterCountsController;
use App\Http\Controllers\Profile\ImguploadController;
use App\Http\Controllers\Frontend\NeighbourhoodController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [DataController::class, 'homepage'])->name('homepage');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/imgupload', [ImguploadController::class, 'update'])->name('profile.profilepic');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get("sendmail", [sendEmail::class, "sendmail"])->name('sendmail');


Route::get("list-location", [DataController::class, "listLocation"])->name('search.location');
/*Route::get("lo-{id}{catid?}", [DataController::class, "singleLocation"])
    ->name('search.results');*/
//Route::get("lo-{id}/{category?}", [DataController::class, "singleLocation"])
   // ->name('search.results');

Route::get("lo-{id}/{category?}", [ExploreController::class, "singleLocation"])
    ->name('search.results');
Route::get("at-{id}", [DataController::class, "explore"])->name('sight.details');
Route::get("landing", [DataController::class, "landing"])->name('landing');
Route::get("recenthistory", [DataController::class, "recenthistory"])->name('recenthistory');

// --------Frontend routes------
Route::get("stays", [DataController::class, "stays"])->name('stays');
/*Route::get("ho-{id}", [DataController::class, "hotel_list"])->name('hotel.list'); */
Route::get("ho-{segment}", [DataController::class, "hotel_list"])->name('hotel.list');
Route::get("hd-{id}", [HotelDetailController::class, "hotel_detail"])->name('hotel.detail');
Route::post("filter_hotel_list", [DataController::class, "filter_hotel_list"]);

// Route::post('/api/save-booking-url', [HotelBookingController::class, 'saveBookingUrl'])->name('save.booking.url');

//filter hotel list
Route::post("hotel_all_filters", [DataController::class, "hotel_all_filters"]);


Route::get("add_hoteldata", [DataController::class, "add_hoteldata"]);

Route::get("save_country", [CountryController::class, "save_country"]);
Route::get("addLocationfaqfont", [DataController::class, "addLocationfaqfont"]);
Route::get("addsightfaqfront", [DataController::class, "addsightfaqfront"]);

Route::post("filtersightbycat", [ExploreController::class, "filtersightbycat"]);
Route::post("add_sightreview", [DataController::class, "add_sightreview"])->name('add_sightreview');

Route::post("add_hotelreview", [DataController::class, "add_hotelreview"])->name('add_hotelreview');

Route::post("/similarhotel", [DataController::class, "similarhotel"]);
Route::post("/getSignature", [DataController::class, "getSignature"]);
Route::post("/edittiming", [DataController::class, "editSighttiming"]);

Route::get('/rd-{id}',[DataController::class,"restaurant"])->name('restaurant_detail');
Route::post('/add_rest_review',[DataController::class,'add_rest_review']);


Route::get('/restaurant_listing/{id}',[DataController::class,"restaurant_listing"])->name('restaurant_listing');
Route::get("search_rest", [DataController::class, "search_rest"]);
Route::get("recenthistory_restaurant", [DataController::class, "recenthistory_restaurant"]);
Route::post("filterrestbycat", [DataController::class, "filterrestbycat"]);
Route::get('/load-more-attractions', [ExploreController::class, "loadMoreAttractions"]);

//explore
Route::post("/add_sight_images", [DataController::class, "add_sight_images"]);
//Route::get("/about-us", [DataController::class, "about_us"])->name('about_us');

//Route::get("/terms", [DataController::class, "term_condition"])->name('term_condition');
//Route::get("/trust-and-safety", [DataController::class, "trust_and_safety"])->name('trust_and_safety');
//Route::get("/career", [DataController::class, "career"])->name('career');
//Route::get("/accessibility-statement", [DataController::class, "accessibility_statement"])->name('accessibility_statement');
//Route::get("/contact-us", [DataController::class, "contact_us"])->name('contact_us');
//Route::get("/privacy_policy", [DataController::class, "privacy_policy"])->name('privacy_policy');


Route::get("/terms", [StaticPagesController::class, "term_condition"])->name('term_condition');
Route::get("/about-us", [StaticPagesController::class, "about_us"])->name('about_us');
Route::get("/trust-and-safety", [StaticPagesController::class, "trust_and_safety"])->name('trust_and_safety');
Route::get("/career", [StaticPagesController::class, "career"])->name('career');
Route::get("/accessibility-statement", [StaticPagesController::class, "accessibility_statement"])->name('accessibility_statement');
Route::get("/contact-us", [StaticPagesController::class, "contact_us"])->name('contact_us');
Route::get("/privacy_policy", [StaticPagesController::class, "privacy_policy"])->name('privacy_policy');


Route::post("/getSignature", [DataController::class, "getSignature"]);


Route::get("/hotels", [DataController::class, "hotel_homepage"])->name('hotel_homepage');
Route::get("/list_hotelsloc", [DataController::class, "list_hotelsloc"]);
Route::get("/recenthotels", [DataController::class, "recenthotels"]);

Route::post("/insert_agency_img", [DataController::class, "donload_agencyimg"]);

Route::get("/insert_hotel_data", [DataController::class, "insert_data"]);
Route::get("/update_location_geo", [DataController::class, "update_location_geo"]);


Route::get("experience-{id}", [DataController::class, "experince"])->name('experince');

//weather

Route::get("/weather", [DataController::class, "weather"])->name('weather');
//stays
Route::post("stays_locdata", [DataController::class, "stayslocdata"]);

//neighbourhood
Route::get("hotel_neighborhood.html", [DataController::class, "hotel_neighbourhood"]);
Route::get("hn-{lid}-{neiborhoodid}-{slug}", [DataController::class, "hotel_neiborhood_listing"])->name('hotel_neibor_list');
Route::post("fetch_neighb_listing_with_api", [DataController::class, "fetch_neighb_listing_with_api"]);
//hotel landing

Route::get("hl-{lid}-{id}-{slug}", [DataController::class, "hotel_landing"])->name('hotel_landing');
Route::get("hotel_landing.html", [DataController::class, "hotel_landing_with_date"]);
Route::post("get_hotel_landing_result", [DataController::class, "get_hotel_landing_result"]);

//neighbourhood
Route::get("nb-{lid}-{slug}", [NeighbourhoodController::class, "neiborhood_listing"])->name('neiborhood_listing');
Route::post("fetch_neighb_listing", [NeighbourhoodController::class, "fetch_neighb_listing"])->name('fetch_neighb_listing');
Route::post("saveTphotel_nearby", [DataController::class, "saveTphotel_nearby"]);
Route::post("saveNearbyhotel_hotellist", [DataController::class, "saveNearbyhotel_hotellist"]);
Route::post("add_sight_nbhotel", [DataController::class, "save_sight_nb_hotel"]);


//hotel listing faq
Route::post("addHotleListingFaq", [HotelController::class, "addHotleListingFaq"]);

Route::post("add_Hotelreview", [HotelController::class, "add_Hotelreview"])->name('add_Hotelreview');

Route::post("getfilteredhotellist", [DataController::class, "getfilteredhotellist"]);
Route::post("gethotellist_withoutdate", [DataController::class, "gethotellist_withoutdate"]);
Route::post("/getwithoutdatedata", [DataController::class, "getwithoutdatedata"]);

//Sight listing
Route::post("/sightlist_saveNearbyhotel", [DataController::class, "sightlist_saveNearbyhotel"]);

Route::post("addHotledetailFaq", [HotelController::class, "addHotledetailFaq"]);


Route::post("hotel_all_filters_without_date", [DataController::class, "hotel_all_filters_without_date"]);

//explore country
Route::get("co-{id}-{slug}", [DataController::class, "explore_country_list"])->name('explore_country_list');
Route::get('/load-more-sight-county', [DataController::class, "loadMoresightbycontry"]);


Route::get("cont-{id}-{slug}", [DataController::class, "explore_continent_list"])->name('explore_continent_list');


Route::get('/loadMoresightbycontinent', [DataController::class, "loadMoresightbycontinent"]);


 Route::get("test_lociq", [HotelController::class, "test_lociq"]);

Route::get("/update_roommnt", [HotelController::class, "update_roommnt"]);
Route::get("/update_roommnt2", [HotelController::class, "update_roommnt2"]);

Route::get("/updatelongtude", [HotelController::class, "updatelongtude"]);
Route::get("/updatelongtude2", [HotelController::class, "updatelongtude2"]);
//
Route::post('/contact_us', [SigninController::class, 'sendContactQuery'])->name('contact.send');
Route::post("search_sights", [DataController::class, "search_sights"]);

Route::post("searchsightlisting", [DataController::class, "searchsightlisting"]);
Route::post("sightlistinghistory", [DataController::class, "sightlistinghistory"]);

Route::post("addnearswimming", [HotelController::class, "addnearswimming"]);


//Restaurant
Route::get("searchr", [DataController::class, "restaurant_landing"])->name('restaurant_landing');
// explore
Route::post('filterReviews',[DataController::class, "filterReviews"]);
Route::post("update_sight_desc", [DataController::class, "update_sight_desc"]);
//end explore


// start hotel detail
Route::post("hotel_room_desc", [DataController::class, "hotel_room_desc"]);
Route::post("hoteldetailnearbyrest", [DataController::class, "hoteldetailnearbyrest"]);
Route::post("hotel_detailfaqs", [DataController::class, "hotel_detailfaqs"]);
Route::get('/spotlight', [HotelsController::class, 'spotlightSection']);

Route::post("add_hoteldetail_nearbyrest", [DataController::class, "add_hoteldetail_nearbyrest"]);

Route::post("hotel_detail_perfect_sight", [DataController::class, "hotel_detail_perfect_sight"]);
Route::post("insert_hotel_desction", [DataController::class, "insert_hotel_desction"]);
Route::post("filter_hotel_room", [DataController::class, "filter_hotel_room"])->name('filter_hotel_room');
Route::post("filter_hotel_room_with_date", [DataController::class, "filter_hotel_room_with_date"])->name('filter_hotel_room_with_date');

Route::post("view_hotel_all_images", [DataController::class, "view_hotel_all_images"])->name('view_hotel_all_images');
Route::post("gethotel_galary_image", [DataController::class, "gethotel_galary_image"])->name('gethotel_galary_image');
Route::post("hotel_detail_nearby_city_and_sights", [DataController::class, "hotel_detail_nearby_city_and_sights"])->name('hotel_detail_nearby_city_and_sights');
// end hotel detail
// --------Sitemap routes------

// --------Sitemap routes from file------
Route::get('/hotel-listing{page}-en-us.xml', function($page) {
    $filePath = public_path("sitemaps/hotel-listing/hotel-listing{$page}-en-us.xml");
    if (File::exists($filePath)) {
        return response()->file($filePath, [
            'Content-Type' => 'text/xml'
        ]);
    }
    return app('App\Http\Controllers\SitemapXmlController')->hotellisting($page);
});


Route::get('/hotel-detail{page}-en-us.xml', function($page) {
    $filePath = public_path("sitemaps/explore-hotel/explore-hotel{$page}-en-us.xml");
    if (File::exists($filePath)) {
        return response()->file($filePath, [
            'Content-Type' => 'text/xml'
        ]);
    }
    return app('App\Http\Controllers\SitemapXmlController')->hoteldetail($page);
});

Route::get('/landing-pages{page}-en-us.xml', function($page) {
    $filePath = public_path("sitemaps/landing-pages/landing-pages{$page}-en-us.xml");
    if (File::exists($filePath)) {
        return response()->file($filePath, [
            'Content-Type' => 'text/xml'
        ]);
    }
    return app('App\Http\Controllers\SitemapXmlController')->generateLandingPagesSitemap($page);
});

Route::get('/explore-locations{page}-en-us.xml', function($page) {
    $filePath = public_path("sitemaps/explore-locations/explore-locations{$page}-en-us.xml");
    if (File::exists($filePath)) {
        return response()->file($filePath, [
            'Content-Type' => 'text/xml'
        ]);
    }
    return app('App\Http\Controllers\SitemapXmlController')->location_listing($page);
});
Route::get('sitemap/landing-pages/{page}', [SitemapXmlController::class, 'generateLandingPagesSitemap'])->name('sitemap.landing-pages');
Route::post('sitemap/clear-cache', [SitemapXmlController::class, 'clearSitemapCache'])->name('sitemap.clear-cache');

// Sitemap URL count check
Route::get("/check-explore-hotel-count", [SitemapXmlController::class, "checkExploreHotelCount"])->name('check.explore.hotel.count');

// Main sitemap routes
Route::get("/sitemap-en-us.xml", [SitemapXmlController::class, "sitemapindex"])->name('sitemapindex');
Route::get("/sitemap.html", [SitemapXmlController::class, "sitemaping"])->name('sitemaping');
//------end sightmap--------




require __DIR__.'/auth.php';


Route::middleware('auth')->group(function () {

	Route::group(['prefix' => 'business'], function(){
		Route::get("/index",[Business_backend::class, "busi_index"])->name('busi_index');
		Route::get("all_busi_users",[Business_backend::class, "all_busi_users"])->name('all_busi_users');
		Route::post("delete_business_users",[Business_backend::class, "delete_business_users"])->name('delete_business_users');
		Route::post("business_active",[Business_backend::class, "business_active"])->name('business_active');

		Route::post("business_status_active",[Business_backend::class, "business_status_active"])->name('business_status_active');
		Route::post("delete_business",[Business_backend::class, "delete_business"])->name('delete_business');

	});

Route::group(['prefix' => 'hotels'], function(){
    Route::get("/hotels", [HotelsController::class, "index"])->name('hotels');
    Route::post("/filter_hotel", [HotelsController::class, "filter_hotel"]);
    Route::get("/edit_hotel/{id}", [HotelsController::class, "edit_hotel"])->name('edit_hotel');
    Route::post('/updateHotel/{id}',[HotelsController::class, 'updateHotel'])->name('updateHotel');
    Route::post('/searchCity',[HotelsController::class, 'searchCity'])->name('searchCity');
    Route::get("/add_hotel", [HotelsController::class, "add_hotel"])->name('add_hotel');
    Route::post("/storeHotel", [HotelsController::class, "storeHotel"])->name('storeHotel');

	Route::get("/edit_review/{id}", [HotelsController::class, "edit_review"])->name('edit_review');
    Route::post("/sortHotelReview", [HotelsController::class, "sortHotelReview"]);
    Route::post("/filterhotelbyid", [HotelsController::class, "filterhotelbyid"]);
    Route::post("/ftrhotelrewview", [HotelsController::class, "ftrhotelrewview"]);
    Route::post("/update_hotelreview", [HotelsController::class, "update_hotelreview"]);

	Route::get("/edit_hotel_faqs/{id}", [HotelsController::class, "edit_hotel_faqs"])->name('edit_hotel_faqs');
    Route::post("/update_hotel_faq", [HotelsController::class, "update_hotel_faq"]);
    Route::post("/add_hotel_faq", [HotelsController::class, "add_hotel_faq"]);

	Route::get('/edit_hotel_category/{id}',[HotelsController::class, "edit_hotel_category"])->name('edit_hotel_category');
    Route::post("/updateHotelCategory", [HotelsController::class, "updateHotelCategory"]);
    Route::post("/search_category", [HotelsController::class, "search_category"]);
    Route::post("/addNewCategory", [HotelsController::class, "addhotelcat"]);

	Route::get('/edit_hotel_landing/{id}',[HotelsController::class, "edit_hotel_landing"])->name('edit_hotel_landing');
    Route::post("/updateLanding", [HotelsController::class, "updateLanding"]);
    Route::post("/hidepage", [HotelsController::class, "hidepage"]);
    Route::post("/delete_landing", [HotelsController::class, "delete_landing"]);
    Route::get('/add_landing_page/{id?}',[HotelsController::class, "add_landing_page"])->name('add_hotel_landing_page');
    Route::post("/search_hotel", [HotelsController::class, "search_hotel"]);
    Route::post("/search_restaurent", [HotelsController::class, "search_restaurent"]);
    Route::post("/search_neighborhood", [HotelsController::class, "search_neighborhood"]);

    Route::post("/search_hotel_amenti", [HotelsController::class, "search_hotel_amenti"]);
    Route::post("/get_Room_type", [HotelsController::class, "get_Room_type"]);
    Route::post("/get_hotel_type", [HotelsController::class, "get_hotel_type"]);
    Route::post("/get_onsight_restaurant", [HotelsController::class, "get_onsight_restaurant"]);
    Route::post("/get_hotel_tags", [HotelsController::class, "get_hotel_tags"]);
    Route::post("/get_public_transit", [HotelsController::class, "get_public_transit"]);
    Route::post("/get_access", [HotelsController::class, "get_access"]);
    Route::post("/store_landing", [HotelsController::class, "store_landing"]);
    Route::post("/update_landingfilter", [HotelsController::class, "update_landingfilter"]);

	 Route::post("/amenties", [HotelsController::class, "amenties"]);
});

Route::group(['prefix' => 'listing'], function(){
    Route::get("/",[ListingController::class, "location"])->name('search_location');
    Route::get("/listing", [ListingController::class, "create"])->name('listing');
    Route::post("/store_listing", [ListingController::class, "store"])->name('store_listing');
    Route::get("/edit_listing/{id}", [ListingController::class, "edit_listing"])->name('edit_listing');
    
   
    
    // Location Content Management Routes
   
     Route::post("/update_listing", [ListingController::class, "update"])->name('update_listing');
    Route::get("/listing_preview/{id}", [ListingController::class, "preview"])->name('listing_preview');
    Route::post("/filter_location", [ListingController::class, "search_location"])->name('filter_location');
    Route::post("/search_parentcon", [ListingController::class, "search_parentcon"])->name('search_parentcon');
    Route::get("/add_faq",[ListingController::class, "add_faq"])->name('add_faq');
    Route::post("/store_loc_faq",[ListingController::class, "store_loc_faq"])->name('store_loc_faq');
    Route::get("/edit_faq/{id}",[ListingController::class, "edit_faq"])->name('edit_faq');

    Route::post("update_editfaq",[ListingController::class, "update_edit"])->name('update_edit');

    Route::post("add_location_faq",[ListingController::class, "add_location_faq"]);
});

Route::group(['prefix' => 'attraction'], function(){
    Route::get("/search_attraction", [AttractionController::class, "index"])->name('search_attraction');
    Route::get("/add_attraction", [AttractionController::class, "create"])->name('add_attraction');
    Route::get("/edit_attraction/{id}", [AttractionController::class, "edit_attraction"])->name('edit_attraction');
    Route::post("/update_att/{id}", [AttractionController::class, "update_att"])->name('update_att');
    Route::post("/store_attraction", [AttractionController::class, "store"])->name('store_attraction');
    Route::post("/search_attracion", [AttractionController::class, "search_attracion"])->name('search_attracion');
    Route::post("/search_city", [AttractionController::class, "search_city"])->name('search_city');
    Route::post("save_timing/{lastinsertedId?}", [AttractionController::class, "save_timing"])->name('save_timing');



    /*---faq---*/
    Route::post("/store_sight_faq", [AttractionController::class, "store_sight_faq"])->name('store_sight_faq');
    Route::post("/search_attr", [AttractionController::class, "search_attr"])->name('search_attr');
    Route::get("/edit_attfaq/{id}", [AttractionController::class, "edit_attfaq"])->name('edit_attfaq');
    Route::post("/update_faq", [AttractionController::class, "update_faq"])->name('update_faq');
    Route::post("/add_sight_faq", [AttractionController::class, "add_sight_faq"])->name('add_sight_faq');

    /*---Category---*/
    Route::get("/add_category", [AttractionController::class, "add_category"])->name('add_category');
    Route::post("/search_attr_cate", [AttractionController::class, "search_attr_category"]);
    Route::post("/save_category", [AttractionController::class, "save_category"])->name('save_category');
    Route::get("/edit_category/{id}", [AttractionController::class, "edit_category"])->name('edit_category');
    Route::post("/update_category/{id}", [AttractionController::class, "update_category"])->name('update_category');

	Route::post("/deleteatt_category", [AttractionController::class, "deleteatt_category"]);
    /*---Reviews--*/
    Route::get("/manage_att_review", [AttractionController::class, "manage_att_review"])->name('manage_att_review');
    Route::post("/filter_manage_att_review", [AttractionController::class, "filter_manage_att_review"]);
    Route::post("/search_attreviews", [AttractionController::class, "search_attreviews"]);
    Route::post("/update_review", [AttractionController::class, "update_review"])->name('update_review');
    Route::get("/edit_reviewbyid/{id}", [AttractionController::class, "edit_reviewbyid"])->name('edit_reviewbyid');
    Route::post("/update_reviewbyid/{id}", [AttractionController::class, "update_reviewbyid"])->name('update_reviewbyid');
    Route::post("/filterreview", [AttractionController::class, "filterreview"])->name('filterreview');
    Route::post("/filter_review_edit", [AttractionController::class, "filter_review_edit"])->name('filter_review_edit');
    Route::post("/filterreview_manage", [AttractionController::class, "filterreview_manage"])->name('filterreview_manage');
    Route::post("/update_markspan", [AttractionController::class, "update_markspan"]);
    /*-------------landing page ------------*/

    Route::get("/att_landingpage", [AttractionController::class, "att_landingpage"])->name('att_landingpage');
    Route::get("/edit_landing/{id}", [AttractionController::class, "edit_landing"])->name('edit_landing');
    Route::get("/edit_sight_img/{id}", [AttractionController::class, "edit_sight_img"])->name('edit_sight_img');

    Route::get("/add_landing_page/{id?}", [AttractionController::class, "add_landing_page"])->name('add_landing_page');
    Route::post("/get_category", [AttractionController::class, "get_category"]);
    Route::post("/get_traveler_type", [AttractionController::class, "get_traveler_type"]);
    Route::post("/store_landing", [AttractionController::class, "store_landing"]);

    Route::post("/update_landing", [AttractionController::class, "update_landing"]);
    Route::post("/hidepage", [AttractionController::class, "hidepage"]);
    Route::post("/delete_landing", [AttractionController::class, "delete_landing"]);

	   /*-------------image------------*/

        Route::get("/edit_sight_img/{id}", [AttractionController::class, "edit_sight_img"])->name('edit_sight_img');
        Route::post("/upload_sight_Image/{id}", [AttractionController::class, "upload_sight_Image"])->name('upload_sight_Image');

        Route::post("/delete_sight_image", [AttractionController::class, "delete_sight_image"]);
        Route::post("/filter_sight_image", [AttractionController::class, "filter_sight_image"]);

	   //--sight listing

        Route::get("/att_detail-{id}", [AttractionController::class, "att_detail"])->name('att_detail');
});

Route::group(['prefix' => 'restaurant'], function(){
    Route::get("/",[RestaurantController::class, "index"])->name('restaurant');
    Route::post("/searchrest",[RestaurantController::class, "searchrest"])->name('searchrest');
    Route::get("/edit_restaurant/{id}",[RestaurantController::class, "edit_restaurant"])->name('edit_restaurant');
    Route::get("/add_restaurant",[RestaurantController::class, "add_restaurant"])->name('add_restaurant');
    Route::post("/store_rest",[RestaurantController::class, "store_rest"])->name('store_rest');
    Route::post("/searchfeature",[RestaurantController::class, "searchfeature"]);
    Route::post("/update_rest/{id}",[RestaurantController::class, "update_rest"])->name('update_rest');
    Route::post("/searchDietary",[RestaurantController::class, "searchDietary"]);
    Route::get("/restaurant_faq/{id}",[RestaurantController::class, "restaurant_faq"])->name('restaurant_faq');

    Route::post("/add_rest_faq",[RestaurantController::class, "add_rest_faq"]);
    Route::post("/update_rest_faq",[RestaurantController::class, "update_rest_faq"]);
    Route::get("/restaurant_cuisine/{id}",[RestaurantController::class, "restaurant_cuisine"])->name('restaurant_cuisine');
    Route::post("/addcuisine",[RestaurantController::class, "addcuisine"]);
    Route::post("/deleterestaurantcus",[RestaurantController::class, "deleterestaurantcus"]);


    Route::get("/editrest_reviews/{id}",[RestaurantController::class, "editrest_reviews"])->name('editrest_reviews');
    Route::post("/update_resteview",[RestaurantController::class, "update_resteview"]);
    Route::post("/ftrrestrewview",[RestaurantController::class, "ftrrestrewview"]);
    Route::post("/sortRestReview",[RestaurantController::class, "sortRestReview"]);
    Route::post("/filterrestbyid",[RestaurantController::class, "filterrestbyid"]);
    Route::post("/deleterestreview",[RestaurantController::class, "delete__rest_review"]);

	Route::get("/edit_review_Image/{id}",[RestaurantController::class, "edit_review_Image"])->name('edit_review_Image');
    Route::post("/upload_rest_review_Image/{id}",[RestaurantController::class, "upload_rest_review_Image"])->name('upload_rest_review_Image');
    Route::post("/delete_review_image",[RestaurantController::class, "delete_review_image"]);
    Route::post("/filterimagebyid",[RestaurantController::class, "filterimagebyid"]);
});
 Route::group(['prefix' => 'landing'], function(){
        Route::get("/",[LandingPageController::class, "index"])->name('landing');
        Route::post("search_landing_page",[LandingPageController::class, "search_landing_page"]);
        Route::post("/update-hotel-landing/{id}", [LandingPageController::class, "updateHotelLanding"])->name('update.hotel.landing');
        Route::get("/edit_attraction_landing/{id}", [LandingPageController::class, "edit_sight_landing"])->name('edit_attraction_landing');
        Route::get("/add_sight_landing_page/{id?}", [LandingPageController::class, "add_sight_landing_page"])->name('add_sight_landing_page');

        Route::get('/edit_hotel_landing/{id}',[LandingPageController::class, "edit_hotel_landing"])->name('edit_hotel_landing');
        Route::get('/add_hotel_landing/{id?}',[LandingPageController::class, "add_hotel_landing"])->name('add_hotel_landing');


        Route::get("add_landing_page_search",[LandingPageController::class, "search_addlanding"])->name('addlandingpage_search');
        Route::post("search_add_landing_page",[LandingPageController::class, "search_add_landing_page"]);

        Route::get("add_landing_page_search",[LandingPageController::class, "search_addlanding"])->name('addlandingpage_search');

	 	/*   hotel listing lading page   */

		 Route::get("add_hotel_listing_landing/{id}",[LandingPageController::class, "add_hotel_listing_landing"])->name('add_hotel_listing_landing');
		 Route::delete('/delete-hotel-hotel-listing-landing/{id}', [LandingPageController::class, 'deleteHotelListing'])->name('delete.hotel.listing.landing');
		 Route::post("store_hotel_listing_landing",[LandingPageController::class, "store_hotel_listing_landing"]);
		 Route::get('/show-all-hotel-landing-listings', [LandingPageController::class, 'showHotelListings'])->name('show-all-hotel-landing-listings');

		 Route::get('/hotel-listing-landing/{id}/edit', [LandingPageController::class, 'editHotelListing'])->name('edit.hotel.listing.landing');
		 Route::post('/updatehotel-listing-landing/{id}', [LandingPageController::class, 'updateHotelListing'])->name('update.hotel.listing.landing');

		 Route::get('/hotel_listing_landing_search', [LandingPageController::class, 'hotel_listing_landing_search'])
			 ->name('hotel_listing_landing_search');

	 	/*  end hotel listing landing page   */
        Route::get("add_exp_landing/{id}",[LandingPageController::class, "add_exp_landing"])->name('add_exp_landing');
        Route::post("search_language",[LandingPageController::class, "search_language"]);
        Route::post("store_exp_landing",[LandingPageController::class, "store_exp_landing"]);

        Route::get("edit_exp_landing/{id}",[LandingPageController::class, "edit_exp_landing"])->name('edit_exp_landing');
        Route::post("update_exp_landing",[LandingPageController::class, "update_exp_landing"]);
        Route::post("hidepage_exp",[LandingPageController::class, "hidepage_exp"]);
        Route::post("delete_landing_exp",[LandingPageController::class, "delete_landing_exp"]);

    });
	 Route::group(['prefix' => 'experience'], function(){
        Route::get("/",[ExperinceController::class, "index"])->name('experience');
        Route::post("search_experince",[ExperinceController::class, "search_experince"]);
        Route::get("add_experience",[ExperinceController::class, "add_experience"])->name('add_experience');
        Route::post("search_language",[ExperinceController::class, "search_language"]);
        Route::post("store_experience",[ExperinceController::class, "store_experience"])->name('store_experience');
        Route::post("searchCity",[ExperinceController::class, "searchCity"]);
        Route::get("edit_experience/{id}",[ExperinceController::class, "edit_experience"])->name('edit_experience');
        Route::post("update_experience",[ExperinceController::class, "update_experience"]);

        Route::get("edit_exp_faq/{id}",[ExperinceController::class, "edit_exp_faq"])->name('edit_exp_faq');
        Route::post("add_exp_faq",[ExperinceController::class, "add_exp_faq"]);
        Route::post("update_exp_faq",[ExperinceController::class, "update_exp_faq"]);

        Route::get("add_Itinerary/{id}",[ExperinceController::class, "add_Itinerary"])->name('add_Itinerary');
        Route::post("store_itinerary",[ExperinceController::class, "store_itinerary"])->name('store_itinerary');
        Route::get("edit_itinerary/{id}",[ExperinceController::class, "edit_itinerary"])->name('edit_itinerary');
        Route::post("update_itinerary",[ExperinceController::class, "update_itinerary"])->name('update_itinerary');
        Route::post("additinerary",[ExperinceController::class, "additinerary"]);

        Route::get("exp_reviews/{id}",[ExperinceController::class, "exp_reviews"])->name('exp_reviews');
        Route::post("update_expreview",[ExperinceController::class, "update_expreview"]);
        Route::post("ftrexprewview",[ExperinceController::class, "ftrexprewview"]);
        Route::post("sortexpReview",[ExperinceController::class, "sortexpReview"]);
        Route::post("filterexpbyid",[ExperinceController::class, "filterexpbyid"]);



        Route::get("edit_exp_category/{id}",[ExperinceController::class, "edit_exp_category"])->name('edit_exp_category');
        Route::post("search_exp_category",[ExperinceController::class, "search_exp_category"]);
        Route::post("addexpcat",[ExperinceController::class, "addexpcat"]);
        Route::post("deleteexp_category",[ExperinceController::class, "deleteexp_category"]);


        Route::get("edit_exp_images/{id}",[ExperinceController::class, "edit_exp_images"])->name('edit_exp_images');
        Route::post("upload_exp_Image/{id}",[ExperinceController::class, "upload_exp_Image"])->name('upload_exp_Image');
        Route::post("delete_exp_image",[ExperinceController::class, "delete_exp_image"]);
        Route::post("filter_exp_image",[ExperinceController::class, "filter_exp_image"]);


    });
	 Route::group(['prefix' => 'reviews'], function(){
        Route::get("/",[ReviewController::class, "index"])->name('reviews');

        Route::post("/search_r_attracion",[ReviewController::class, "search_r_attracion"]);
        Route::get('/edit_att_review/{id}',[ReviewController::class, "edit_review"])->name('edit_att_review');

        Route::post('/filter_r_hotel',[ReviewController::class, "filter_r_hotel"]);
        Route::get('/edit_hotel_review/{id}',[ReviewController::class, "edithotel_review"])->name('edithotel_review');


        Route::post('/searchr_restaurant',[ReviewController::class, "searchr_restaurant"]);
        Route::get('/edit_restaurant_reviews/{id}',[ReviewController::class, "edit_restaurant_reviews"])->name('edit_restaurant_reviews');


        Route::post('/searchr_experience',[ReviewController::class, "searchr_experience"]);
        Route::get('/edit_exp_reviews/{id}',[ReviewController::class, "edit_exp_reviews"])->name('edit_exp_reviews');

    });
  Route::group(['prefix' => 'faq'], function(){
        Route::get("/",[FaqController::class, "index"])->name('manage_faqs');
        Route::get('/edit_att_faq/{id}',[FaqController::class, "edit_att_faq"])->name('edit_att_faq');
        Route::post('/searchfaqattracion',[FaqController::class, "searchfaqattracion"]);


        Route::post('/filter_faq_hotel',[FaqController::class, "filter_faq_hotel"]);
        Route::get('/edit_hotel_faq/{id}',[FaqController::class, "edit_hotel_faq"])->name('edit_hotel_faq');

        Route::post('/search_faq_restaurant',[FaqController::class, "search_faq_restaurant"]);
        Route::get('/edit_restaurant_faq/{id}',[FaqController::class, "edit_restaurant_faq"])->name('edit_restaurant_faq');


        Route::post('/search_faq_experience',[FaqController::class, "search_faq_experience"]);
        Route::get('/edit_experience_faq/{id}',[FaqController::class, "edit_experience_faq"])->name('edit_experience_faq');

    });
    Route::group(['prefix' => 'category'], function(){
        Route::get("/",[CategoryController::class, "index"])->name('manage_category');
        Route::post("/search_cat_attracion",[CategoryController::class, "search_cat_attracion"]);
        Route::get('/edit_att_category/{id}',[CategoryController::class, "edit_att_category"])->name('edit_att_category');

        Route::post("/search_cat_hotel",[CategoryController::class, "search_cat_hotel"]);
        Route::get('/edit_hotelcategory/{id}',[CategoryController::class, "edit_hotelcategory"])->name('edit_hotelcategory');

        Route::post("/search_cat_experience",[CategoryController::class, "search_cat_experience"]);
        Route::get('/edit_experience_category/{id}',[CategoryController::class, "edit_experience_category"])->name('edit_experience_category');


    });
	  Route::group(['prefix' => 'users'], function(){
        Route::get("/",[UserController::class, "index"])->name('users');
        Route::post("/search_user",[UserController::class, "search_user"]);

        Route::get('/edit_user/{id}',[UserController::class, "edit_user"])->name('edit_user');
        Route::post('/update_user',[UserController::class, "update_user"]);

        Route::get('/add_user',[UserController::class, "add_user"])->name('add_user');
        Route::post('/store_user',[UserController::class, "store_user"])->name('store_user');

	    //admin user


        Route::get("/admin_user",[UserController::class, "user_index"])->name('user_index');
        Route::post("/search_admin_user",[UserController::class, "search_admin_user"]);
        Route::get('/add_admin_user',[UserController::class, "add_admin_user"])->name('add_admin_user');
        Route::get('/edit_admin_user/{id}',[UserController::class, "edit_admin_user"])->name('edit_admin_user');
        Route::post('/store_admin_user',[UserController::class, "store_admin_user"])->name('store_admin_user');
        Route::post('/update_admin_user',[UserController::class, "update_admin_user"]);

	    Route::get('/all_admin_user',[UserController::class,"all_admin_user"])->name('all_admin_user');
        Route::post('/admin_active',[UserController::class,"admin_active"]);
        Route::post('/delete_admin_users',[UserController::class,"delete_admin_users"]);

    });

});

Route::get("all_urls", [UrlController::class, "urlindex"])->name('urlindex');
Route::get("add_url", [UrlController::class, "add_url"])->name('add_url');
Route::post('/form', [UrlController::class, 'processForm'])->name('form.process');
Route::post('/update-urls', [UrlController::class, 'updateurlval'])->name('url_udate');
Route::post("updatemetadesc", [DataController::class, "updatemetadesc"])->name('updatemetadesc');



//business

Route::post('/save_user', [BusinessController::class, 'save_user'])->name('save_user');
Route::post("searchLocation", [BusinessController::class, "searchLocation"])->name('searchLocation');

Route::post("searchbusiness", [BusinessController::class, "searchbusiness"])->name('searchbusiness');
Route::post("loginbusiness", [BusinessController::class, "loginbusiness"])->name('loginbusiness');

// Route::middleware('auth:business_user')->group(function () {
//     Route::get("business_dashboard", [BusinessController::class, "dashboard"])->name('business_dashboard');
//     Route::get("logout", [BusinessController::class, "logout"])->name('logout');

// });

// Route::middleware(['guest:business_user'])->group(function () {

//     Route::get('/businessindex', [BusinessController::class, 'businessindex'])->name('businessindex');

//     // ... other guest routes
// });

  Route::middleware('business_auth')->group(function () {
        Route::get("business_dashboard", [BusinessController::class, "dashboard"])->name('business_dashboard');
        Route::get("view_bussines-{id}-{slug}", [BusinessController::class, "view_bussines"])->name('view_bussines');

        Route::get("logout", [BusinessController::class, "logout"])->name('logout');
        Route::get("add_photos-{id}-{slug}", [BusinessController::class, "add_photos"])->name('add_photos');


        //profile
        Route::get("edit-business-profile", [BusinessController::class, "edit_business_profile"])->name('edit_business_profile');
        Route::post("/bus_updateprofile", [BusinessController::class, "bus_updateprofile"])->name('bus_updateprofile');

        Route::post("/busi_changeprofileimg", [BusinessController::class, "busi_changeprofileimg"])->name('busi_changeprofileimg');

         //end profile



         Route::post("/add_sight_busi_img", [BusinessController::class, "add_sight_busi_img"])->name('add_sight_busi_img');

         //view business hotel

         Route::get("view_hotel_business-{id}-{slug}", [BusinessController::class, "view_hotel_business"])->name('view_hotel_business');
         Route::get("claim_listing-{id}-{type}", [BusinessController::class, "claim_listing"])->name('claim_listing');


          Route::post("claimlist_save", [BusinessController::class, "claimlist_save"])->name('claimlist_save');
          Route::get("choose_uploadid-{id}-{type}", [BusinessController::class, "choose_uploadid"])->name('choose_uploadid');

          Route::post("update-hotel-data", [BusinessController::class, "update_hotel_data"])->name('update_hotel_data');

          Route::post("update-hotel-amenity", [BusinessController::class, "update_hotel_amenity"])->name('update_hotel_amenity');
          Route::post("update_hotel_contact", [BusinessController::class, "update_hotel_contact"])->name('update_hotel_contact');

          Route::get("edit_business_location-{id}", [BusinessController::class, "edit_business_location"])->name('edit_business_location');

          Route::post("update_map_data", [BusinessController::class, "update_map_data"])->name('update_map_data');

          Route::get("add_hotel_images-{id}", [BusinessController::class, "add_hotel_images"])->name('add_hotel_images');
	      Route::post("save_add_hotel_images", [BusinessController::class, "save_add_hotel_images"])->name('save_add_hotel_images');

          //all reviews

          Route::get("all_reviews-{id}", [BusinessController::class, "all_reviews"])->name('all_reviews');
          Route::post("image_vatification", [BusinessController::class, "image_vatification"])->name('image_vatification');

	     //phone varification

          //phone varification

          Route::post("send_business_sms", [BusinessController::class, "send_business_sms"])->name('send_business_sms');
          Route::get("view_otp-{phone}", [BusinessController::class, "view_otp"])->name('view_otp');

          Route::post("verifyOtp", [BusinessController::class, "verifyOtp"])->name('verifyOtp');
          Route::get("sightreviews-{id}", [BusinessController::class, "sightreviews"])->name('sightreviews');

	      /*-------------------restaurant------------------------*/

          Route::get("view_rest_business-{id}-{slug}", [BusinessController::class, "view_rest_business"])->name('view_rest_business');
          Route::post("edit-gi-rest", [BusinessController::class, "edit_ginfo_rest"])->name('edit_ginfo_rest');
          Route::post("update_restaurant_contact", [BusinessController::class, "update_restaurant_contact"])->name('update_restaurant_contact');
          Route::get("edit_brestaurant_location-{id}", [BusinessController::class, "edit_brestaurant_location"])->name('edit_brestaurant_location');

          Route::post("update_rest_map_data", [BusinessController::class, "update_rest_map_data"])->name('update_rest_map_data');

          Route::get("edit_rest_cuisine-{id}", [BusinessController::class, "edit_rest_cuisine"])->name('edit_rest_cuisine');
          Route::post("update_rest_cuisine", [BusinessController::class, "update_rest_cuisine"])->name('update_rest_cuisine');



	    //rest
          Route::get("rest_all_reviews-{id}", [BusinessController::class, "rest_all_reviews"])->name('rest_all_reviews');

          Route::get("add_rest_photos-{id}", [BusinessController::class, "add_rest_photos"])->name('add_rest_photos');
          Route::post("bus_add_rest_images", [BusinessController::class, "bus_add_rest_images"])->name('bus_add_rest_images');
          Route::post("delete_rest_image", [BusinessController::class, "delete_rest_image"])->name('delete_rest_image');
          Route::post("update_menu_link", [BusinessController::class, "update_menu_link"])->name('update_menu_link');
          Route::get("add_rest_menu-{id}", [BusinessController::class, "add_rest_menu"])->name('add_rest_menu');

          Route::post("update_menu", [BusinessController::class, "update_menu"])->name('update_menu');
          Route::post("delete_sight_image", [BusinessController::class, "delete_sight_image"])->name('delete_sight_image');
          Route::get("edit_sight_info-{id}", [BusinessController::class, "edit_sight_info"])->name('edit_sight_info');
          Route::post("save_sight_info", [BusinessController::class, "save_sight_info"])->name('save_sight_info');

          Route::get("edit_sight_location-{id}", [BusinessController::class, "edit_sight_location"])->name('edit_sight_location');

          Route::post("update_sight_map_data", [BusinessController::class, "update_sight_map_data"])->name('update_sight_map_data');


          Route::post("delete_hotel_image", [BusinessController::class, "delete_hotel_image"])->name('delete_hotel_image');

  });
    Route::get("createbusiness", [BusinessController::class, "createbusiness"])->name('createbusiness');
    // Route::middleware(['guest:business_user'])->group(function () {



//     // ... other guest routes
// });

Route::post('/upload_img', [BusinessController::class, 'upload_img'])->name('upload_img');

Route::get('/businessindex', [BusinessController::class, 'businessindex'])->name('businessindex');


Route::post("searchaddress", [BusinessController::class, "searchaddress"])->name('searchaddress');
Route::post("save_business", [BusinessController::class, "save_business"])->name('save_business');

Route::get("/business_varification", [BusinessController::class, "business_varification"])->name('business_varification');

//start forgot password
Route::get("/business-forgot-password", [BusinessController::class, "buss_forgot_password"])->name('buss_forgot_password');
Route::post("/busi_sendlink_forgorpass", [BusinessController::class, "busi_sendlink_forgorpass"])->name('busi_sendlink_forgorpass');
Route::get("/business-reset-password/{token}", [BusinessController::class, "busi_reset_password"])->name('busi_reset_password');

Route::post("/busi_save_reset_password", [BusinessController::class, "busi_save_reset_password"])->name('busi_save_reset_password');


//end business
    // ... other guest routes


Route::post("searchaddress", [BusinessController::class, "searchaddress"])->name('searchaddress');
Route::post("save_business", [BusinessController::class, "save_business"])->name('save_business');

Route::get("/business_varification", [BusinessController::class, "business_varification"])->name('business_varification');


 // start weather

 Route::get("/weatherforcast", [WeatherController::class, "weatherindex"]);

 Route::post("/searchloc", [WeatherController::class, "searchloc"]);
 Route::get("/getwearapi", [WeatherController::class, "getwearapi"]);
 Route::get('/weather-widget',[WeatherController::class, "weather_widget"])->name('weather_widget');
 Route::get('/createwidget', [WeatherController::class, 'createwidget']);
 // end weather


Route::get("/update_hotel_overview", [HotelController::class, "update_hotel_overview"]);


//  frontend signup start

Route::get("/signup", [SigninController::class, "signup"])->name('signup');
Route::post("/register_user", [SigninController::class, "register_user"])->name('register_user');
Route::get("/user_login", [SigninController::class, "user_login"])->name('user_login');
Route::get("/varification", [SigninController::class, "varification"])->name('varification');
Route::post("/userlongin", [SigninController::class, "userlongin"])->name('userlongin');

Route::post("/checkemail", [SigninController::class, "checkemail"])->name('checkemail');
Route::post("/check_email_exists", [SigninController::class, "checkEmailExists"])->name('checkEmailExists');
Route::post("/search_city", [SigninController::class, "search_city"])->name('search_city');



Route::get("/user_dashboard", [SigninController::class, "front_dashboard"])->name('user_dashboard');
Route::get("/userlogout", [SigninController::class, "userlogout"])->name('userlogout');
Route::post("/search_loc_city", [SigninController::class, "search_loc_city"])->name('search_loc_city');

Route::post("/updateprofile", [SigninController::class, "updateprofile"])->name('updateprofile');

Route::post("/addpost", [SigninController::class, "addpost"])->name('addpost');


Route::post("/search_loc_citys", [SigninController::class, "search_loc_citys"])->name('search_loc_citys');

Route::post("/changeprofileimg", [SigninController::class, "changeprofileimg"])->name('changeprofileimg');

Route::post('/search-location', [AttractionController::class, 'search'])->name('search_location');



Route::get("/forgot_password", [SigninController::class, "forgot_password"])->name('forgot_password');

Route::post("/sendlink_forgorpassword", [SigninController::class, "sendlink_forgorpassword"])->name('sendlink_forgorpassword');
Route::get("/reset_password/{token}", [SigninController::class, "reset_password"])->name('reset_password');

Route::post("/save_reset_password", [SigninController::class, "save_reset_password"])->name('save_reset_password');


//  frontend signup start

Route::get('/hotels', [HotelsController::class, 'searchForm'])->name('search.form');
Route::post('/hotels/search', [HotelsController::class, 'search'])->name('search.perform');

// routes/web.php
Route::get('/search-hotel', [HotelController::class, 'search'])->name('searchHotel');
Route::post('/users/update/{id}', [UserController::class, 'update']);

Route::post('/update-sight', [DataController::class, 'updateSight']);


Route::get('/search', [HotelsController::class, 'searchForm'])->name('search.form');
Route::post('/search', [HotelsController::class, 'search'])->name('search.perform');

Route::post('/users/store', [UserController::class, 'store_user'])->name('store_user');
	// Route::get('/show-all-listings', [YourController::class, 'showAllListing'])->name('showAllListing');

Route::post('/store-hotel-listing-landing-csv', [LandingPageController::class, 'store_hotel_listing_landing_csv'])->name('store_hotel_listing_landing_csv');

Route::get('/user_created', function () {
    return view('Users.user_created', [
        'username' => session('username'),
        'password' => session('password')
    ]);
})->name('user_created');

Route::get('/restaurant/edit/{RestaurantId}', [LandingPageController::class, 'edit'])->name('edit_restaurant_landing');
Route::get('/experience/edit/{ExperienceId}', [LandingPageController::class, 'edit'])->name('edit_exp_landing');
Route::get('/restaurant/edit/{id}', [LandingPageController::class, 'edit'])->name('edit_restaurant');
Route::post('/restaurant/update-landing', [LandingPageController::class, 'update_restaurant_landing'])->name('update_restaurant_landing');

Route::post('/update-sight', [DataController::class, 'updateSight'])->name('update.sight');
Route::get('/landing/show_all_hotel_listing_landing/{id}', [LandingPageController::class, 'showHotelListings'])->name('show_hotel_listings');
Route::get("landing/search_landing_page", [LandingPageController::class, "search_landing_page"])->name('search_landing_page');
Route::get('/hotel-listing/edit/{id}', [LandingPageController::class, 'edit'])->name('edit_hotel_listing_landing');
Route::get('/add-rest-landing', [LandingPageController::class, 'add_rest_landing'])->name('add_rest_landing');

Route::get('/landing/filter', [LandingPageController::class, 'filter'])->name('landing.filter');

Route::post('/admin/location-seo/update', [LocationSeoController::class, 'update'])
    ->name('location.seo.update')
    ->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::get("/admin/location-seo", [App\Http\Controllers\LocationSeoController::class, 'index'])->name('admin.location.seo');
    Route::post("/admin/location-seo/update", [App\Http\Controllers\LocationSeoController::class, 'update'])->name('admin.location.seo.update');
});
Route::get("{city}-hotels-with-{amenity}", [DataController::class, "searchByAmenity"])->name('seo.hotel.amenity');
Route::post('/store-hotel-listing-landing-csv', [LandingPageController::class, 'store_hotel_listing_landing_csv'])->name('store_hotel_listing_landing_csv');
Route::post('/store-highlight-review', [DataController::class, 'storeHighlightReview'])->name('store.highlight.review');
Route::get("hotel-room-desc", [DataController::class, "hotel_room_desc"])->name('hotel.room.desc');
Route::get('/location/{id}/slug', [LandingPageController::class, 'getLocationSlug']);

// In routes/web.php or routes/api.php
Route::get('/api/search-slug/{type}/{name}', [LandingPageController::class, 'searchSlug']);
Route::get('/sitemap/static/{pagenumber}', [SitemapXmlController::class, 'staticSitemap']);
Route::get('/landing/filter', [LandingPageController::class, 'filterLanding'])->name('landing.filter');
Route::get('/landing/autosuggest', [LandingPageController::class, 'autoSuggest'])->name('landing.autosuggest');

Route::get('/search-nearby-places', [DataController::class, 'searchNearbyPlaces'])->name('search.nearby.places');
//Route::get('/hotels-near-sight/{sightId}', [HotelNearbyController::class, 'getHotelsNearSight'])->name('hotels.near.sight');
//Route::get('/hotels-near-sight/{sightId}/{radius?}', [HotelNearbyController::class, 'getHotelsNearSight'])->name('hotels.near.sight');


Route::get('/ho-{slug}/{extra?}', function ($slug) {
    return redirect("/ho-{$slug}", 301);
})->where('extra', '.*');

Route::get('/lo-{slug}', function ($slug, Request $request) {
    // If query parameters exist, redirect to clean URL
    if ($request->query()) {
        return redirect(url("/lo-{$slug}"), 301);
    }
    return view('listing', ['slug' => $slug]); // Replace with actual logic
});

// URL Content Management Routes
Route::middleware(['auth'])->group(function () {
    Route::prefix('url-management')->group(function () {
        // Admin only routes
        Route::get('/qa-dashboard', [UrlContentController::class, 'showQaDashboard'])->name('url.qa-dashboard');
        Route::post('/content/accept', [UrlContentController::class, 'acceptContent'])->name('url.content.accept');
    });
});

Route::prefix('url-management')->group(function () {
    // Regular user routes
    Route::get('/upload', [UrlListingController::class, 'showUploadForm'])->name('url.upload');
    Route::post('/upload', [UrlListingController::class, 'uploadUrls'])->name('url.upload.store');
    Route::get('/listing', [UrlListingController::class, 'showListings'])->name('url.listing');
    Route::post('/content', [UrlContentController::class, 'store'])->name('url.content.store');
    Route::get('/search', [UrlListingController::class, 'search'])->name('url.search');
    Route::post('/content/update', [UrlContentController::class, 'requestUpdate'])->name('url.content.update');
});

Route::middleware(['auth'])->group(function () {
    // Regular user routes
    Route::get('/url-upload', [UrlListingController::class, 'showUploadForm'])->name('url.upload');
    Route::post('/api/upload_urls', [UrlListingController::class, 'uploadUrls'])->name('url.upload.store');

    Route::get('/url-listing', [UrlListingController::class, 'showListings'])->name('url.listing');
    Route::get('/api/listing', [UrlListingController::class, 'getListing'])->name('url.listing.get');

    Route::get('/url-search', [UrlListingController::class, 'search'])->name('url.search');

    Route::post('/api/content', [UrlContentController::class, 'store'])->name('url.content.store');
    Route::post('/api/request_update', [UrlContentController::class, 'requestUpdate'])->name('url.content.update');

    // QA Manager routes
    Route::middleware(['role:qa_manager'])->group(function () {
        Route::get('/qa-dashboard', [UrlContentController::class, 'showQaDashboard'])->name('url.qa-dashboard');
        Route::get('/api/pending_contents', [UrlContentController::class, 'getPendingContents'])->name('url.pending.contents');
        Route::post('/api/accept_content', [UrlContentController::class, 'acceptContent'])->name('url.content.accept');
    });
});

Route::middleware(['auth'])->group(function () {
    // URL Management Routes
    Route::get('/url-upload', [UrlListingController::class, 'showUploadForm'])->name('url.upload');
    Route::post('/api/upload_urls', [UrlListingController::class, 'uploadUrls'])->name('url.upload.store');
    Route::get('/url-listing', [UrlListingController::class, 'showListings'])->name('url.listing');
    Route::get('/api/listings', [UrlListingController::class, 'getListing'])->name('url.listing.get');
    Route::post('/api/content', [UrlContentController::class, 'store'])->name('url.content.store');
    Route::get('/api/listings/{listingId}/content', [UrlContentController::class, 'getListingContent'])->name('url.listing.content');

    // QA Dashboard Routes
    Route::get('/qa-dashboard', [UrlContentController::class, 'showQaDashboard'])->name('url.qa.dashboard');
    Route::get('/api/pending-contents', [UrlContentController::class, 'getPendingContents'])->name('url.qa.pending');
    Route::post('/api/accept-content', [UrlContentController::class, 'acceptContent'])->name('url.qa.accept');
    Route::post('/api/request-update', [UrlContentController::class, 'requestUpdate'])->name('url.qa.update');
});
Route::post('/save-booking-url', [DataController::class, 'saveBookingUrl']);
Route::get('/fetch-hotels', [DataController::class, 'ajaxFetchHotels']);
Route::get('/hotel-filter-counts', [HotelFilterCountsController::class, 'getFilterCounts']);

// Test route for location content update
Route::get('/test-location-update', function () {
    return view('test-location-update');
});

Route::get('/import-locations', function () {
\App\Jobs\ImportSightsJob::dispatch();
    return 'Job dispatched!';
});

Route::get('/import-hotels', function () {
    \App\Jobs\ImportHotelsJob::dispatch();
    return 'Hotel import job dispatched!';
});

Route::get('/import-experiences', function () {
    \App\Jobs\ImportExperiencesJob::dispatch();
    return 'Experience import job dispatched!';
});


Route::post('/hotel/transportation', [DataController::class, 'getNearbyTransportation'])->name('hotel.transportation');
// listing quick Portrait Show
Route::get('/listing/{city}', [ExploreController::class, 'showListing']);



