<?php

use Illuminate\Support\Facades\Route;

// Routes accessible by super-admin only
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users');
    Route::get('/admin_user', [App\Http\Controllers\UserController::class, 'user_index'])->name('user_index');
    Route::get('/manage-category', [App\Http\Controllers\CategoryController::class, 'index'])->name('manage_category');
    Route::get("/index",[App\Http\Controllers\Business_backend::class, "busi_index"])->name('busi_index');
    Route::get("all_busi_users",[App\Http\Controllers\Business_backend::class, "all_busi_users"])->name('all_busi_users');
    Route::get('/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews');
    Route::get('/faqs', [App\Http\Controllers\FaqController::class, 'index'])->name('manage_faqs');
    Route::get('/locations', [App\Http\Controllers\ListingController::class, 'location'])->name('search_location');
     Route::get("/search_attraction", [App\Http\Controllers\AttractionController::class, "index"])->name('search_attraction');
    Route::get('/hotels', [App\Http\Controllers\HotelController::class, 'index'])->name('hotels');
    Route::get('/restaurants', [App\Http\Controllers\RestaurantController::class, 'index'])->name('restaurant');
    Route::get("/experience",[App\Http\Controllers\ExperinceController::class, "index"])->name('experience'); 
    Route::get("/landing",[App\Http\Controllers\LandingPageController::class, "index"])->name('landing'); 

	// Location Content Routes
    Route::get('/location/{id}/content', [App\Http\Controllers\ListingController::class, 'getLocationContent'])->name('get_location_content');
    Route::post('/location/update-content', [App\Http\Controllers\ListingController::class, 'updateLocationContent'])->name('update_location_content');

	 // Q&A Routes
    Route::get('/qa_attraction/{id}', [App\Http\Controllers\FaqController::class, 'qa_attraction'])->name('qa_attraction');
   
    Route::post('/store_attraction_qa', [App\Http\Controllers\FaqController::class, 'store_attraction_qa'])->name('store_attraction_qa');
    Route::put('/update_attraction_qa', [App\Http\Controllers\FaqController::class, 'update_attraction_qa'])->name('update_attraction_qa');
    Route::delete('/delete_attraction_qa', [App\Http\Controllers\FaqController::class, 'delete_attraction_qa'])->name('delete_attraction_qa');
    Route::post('/store_hotel_qa', [App\Http\Controllers\FaqController::class, 'store_hotel_qa'])->name('store_hotel_qa');
  
});

// Routes accessible by admin and super-admin
Route::middleware(['auth', 'role:admin,super-admin'])->group(function () {
    Route::get('/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews');
    Route::get('/faqs', [App\Http\Controllers\FaqController::class, 'index'])->name('manage_faqs');
	Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users');
    Route::get("/admin_user",[App\Http\Controllers\UserController::class, "user_index"])->name('user_index');  
    Route::get('/manage-category', [App\Http\Controllers\CategoryController::class, 'index'])->name('manage_category');
    Route::get("/index",[App\Http\Controllers\Business_backend::class, "busi_index"])->name('busi_index');
    Route::get("all_busi_users",[App\Http\Controllers\Business_backend::class, "all_busi_users"])->name('all_busi_users');
    Route::get('/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews');
    Route::get('/faqs', [App\Http\Controllers\FaqController::class, 'index'])->name('manage_faqs');
    Route::get('/locations', [App\Http\Controllers\ListingController::class, 'location'])->name('search_location');
    Route::get("/search_attraction", [App\Http\Controllers\AttractionController::class, "index"])->name('search_attraction');
    Route::get('/hotels', [App\Http\Controllers\HotelController::class, 'index'])->name('hotels');
    Route::get('/restaurants', [App\Http\Controllers\RestaurantController::class, 'index'])->name('restaurant');
    Route::get("/experience",[App\Http\Controllers\ExperinceController::class, "index"])->name('experience'); 
    Route::get("/landing",[App\Http\Controllers\LandingPageController::class, "index"])->name('landing'); 
});

// Routes accessible by editor, admin and super-admin
Route::middleware(['auth', 'role:editor,admin,super-admin'])->group(function () {
    Route::get("/search_attraction", [App\Http\Controllers\AttractionController::class, "index"])->name('search_attraction');
    Route::get('/hotels', [App\Http\Controllers\HotelController::class, 'index'])->name('hotels');
    Route::get('/restaurants', [App\Http\Controllers\RestaurantController::class, 'index'])->name('restaurant');
    Route::get("/experience",[App\Http\Controllers\ExperinceController::class, "index"])->name('experience'); 
	Route::get('/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews');
    Route::get('/faqs', [App\Http\Controllers\FaqController::class, 'index'])->name('manage_faqs');
});
