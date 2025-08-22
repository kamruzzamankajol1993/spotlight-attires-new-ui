<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;


Route::controller(FrontController::class)->group(function () {

    Route::get('/', 'index')->name('home.index');
    Route::get('/category/{slug}', 'category')->name('category.show');
    Route::get('/subcategory/{slug}', 'subcategory')->name('subcategory.show');
    // ADD THESE NEW ROUTES FOR THE OFFER PAGE
    Route::get('/offer/{slug}','offer')->name('offer.show');
    Route::get('/offers/filter','filterOffers')->name('offer.filter');

    Route::get('/animation-category/{slug}', 'animationCategory')->name('animation.category.show');
    Route::get('/animation-category-filter', 'filterAnimationCategory')->name('animation.category.filter');

    Route::get('/shop', 'shop')->name('shop.show');
    Route::get('/shop-filter', 'ajaxShopFilter')->name('shop.ajax_filter');
    Route::get('products-filter', 'filterProducts')->name('products.filter');

});
