<?php

use App\Http\Controllers\Business;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExperinceController;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LocationSeoController;

// Routes accessible by super-admin only
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/admin_user', [UserController::class, 'user_index'])->name('user_index');
    Route::get('/manage-category', [CategoryController::class, 'index'])->name('manage_category');
    Route::get("/index",[App\Http\Controllers\Business_backend::class, "busi_index"])->name('busi_index');
    Route::get("all_busi_users",[App\Http\Controllers\Business_backend::class, "all_busi_users"])->name('all_busi_users');
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::get('/faqs', [FaqController::class, 'index'])->name('manage_faqs');
    Route::get('/locations', [App\Http\Controllers\ListingController::class, 'location'])->name('search_location');
     Route::get("/search_attraction", [AttractionController::class, "index"])->name('search_attraction');
    Route::get('/hotels', [App\Http\Controllers\HotelController::class, 'index'])->name('hotels');
    Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurant');
    Route::get("/experience",[ExperinceController::class, "index"])->name('experience');
    Route::get("/landing",[LandingPageController::class, "index"])->name('landing');

    // Location Content Routes
    Route::get('/location/{id}/content', [ListingController::class, 'getLocationContent'])->name('get_location_content');
    Route::post('/location/update-content', [ListingController::class, 'updateLocationContent'])->name('update_location_content');

    // FAQ and Q&A Routes
    Route::get('/qa_attraction/{id}', [FaqController::class, 'qa_attraction'])->name('qa_attraction');
    Route::get('/qa_hotel/{id}', [FaqController::class, 'qa_hotel'])->name('qa_hotel');
    Route::post('/store_attraction_qa', [FaqController::class, 'store_attraction_qa'])->name('store_attraction_qa');
    Route::put('/update_attraction_qa', [FaqController::class, 'update_attraction_qa'])->name('update_attraction_qa');
    Route::delete('/delete_attraction_qa', [FaqController::class, 'delete_attraction_qa'])->name('delete_attraction_qa');
    Route::post('/store_hotel_qa', [FaqController::class, 'store_hotel_qa'])->name('store_hotel_qa');
    Route::put('/update_hotel_qa', [FaqController::class, 'update_hotel_qa'])->name('update_hotel_qa');
    Route::delete('/delete_hotel_qa', [FaqController::class, 'delete_hotel_qa'])->name('delete_hotel_qa');

});

// Routes accessible by admin and super-admin
Route::middleware(['auth', 'role:admin,super-admin'])->group(function () {
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::get('/faqs', [FaqController::class, 'index'])->name('manage_faqs');
	Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get("/admin_user",[UserController::class, "user_index"])->name('user_index');
    Route::get('/manage-category', [CategoryController::class, 'index'])->name('manage_category');
    Route::get("/index",[App\Http\Controllers\Business_backend::class, "busi_index"])->name('busi_index');
    Route::get("all_busi_users",[App\Http\Controllers\Business_backend::class, "all_busi_users"])->name('all_busi_users');
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::get('/faqs', [FaqController::class, 'index'])->name('manage_faqs');
    Route::get('/locations', [App\Http\Controllers\ListingController::class, 'location'])->name('search_location');
    Route::get("/search_attraction", [AttractionController::class, "index"])->name('search_attraction');
    Route::get('/hotels', [App\Http\Controllers\HotelController::class, 'index'])->name('hotels');
    Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurant');
    Route::get("/experience",[ExperinceController::class, "index"])->name('experience');
    Route::get("/landing",[LandingPageController::class, "index"])->name('landing');
});

// Routes accessible by editor, admin and super-admin
Route::middleware(['auth', 'role:editor,admin,super-admin'])->group(function () {
    Route::get("/search_attraction", [AttractionController::class, "index"])->name('search_attraction');
    Route::get('/hotels', [HotelController::class, 'index'])->name('hotels');
    Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurant');
    Route::get("/experience",[ExperinceController::class, "index"])->name('experience');
	Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::get('/faqs', [FaqController::class, 'index'])->name('manage_faqs');
});
