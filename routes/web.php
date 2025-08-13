<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;


Route::controller(FrontController::class)->group(function () {

    Route::get('/', 'index')->name('home.index');

});
