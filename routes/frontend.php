<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HotelBookingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Profile\ImguploadController;
use App\Http\Controllers\StaticPagesController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\HotelsController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\SitemapXmlController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ExperinceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Jobs\ImportDataJob;
use App\Jobs\ImportExperienceJob;
use App\Jobs\ImportSightsJob;
use App\Http\Controllers\Frontend\NeighbourhoodController;
use App\Http\Controllers\Frontend\HotelController;
use App\Http\Controllers\url\UrlController;
use App\Http\Controllers\Frontend\BusinessController;
use App\Http\Controllers\Frontend\WeatherController;
use App\Http\Controllers\sendEmail;
use App\Http\Controllers\Frontend\SigninController;
use App\Http\Controllers\Business_backend;
use App\Http\Controllers\ExploreController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\UrlListingController;
use App\Http\Controllers\UrlContentController;
use App\Http\Controllers\HotelFilterCountsController;
use App\Http\Controllers\HotelDetailController;
use App\Http\Controllers\HOSegmentController;
use App\Http\Controllers\Profile\SettingsController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\GoogleController;

// All public (frontend) routes from web.php, lines 61-400 (excluding admin/api)
Route::pattern('localeSlug', '[a-z]{2}-[a-z]{2}');
Route::get('/', [DataController::class, 'homepage'])->name('homepage');
Route::get('lo-{id}/{category?}', [ExploreController::class, 'singleLocation'])->name('search.results');
Route::get('/dashboard', function () { return view('dashboard'); })->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/imgupload', [ImguploadController::class, 'update'])->name('profile.profilepic');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('sendmail', [sendEmail::class, 'sendmail'])->name('sendmail');
Route::get('list-location', [DataController::class, 'listLocation'])->name('search.location');
Route::get('list-location', [DataController::class, 'listLocation'])->name('search.location');
// ... (all public routes up to line 799 have been migrated above)
// If any public routes exist after line 800, add them here.
