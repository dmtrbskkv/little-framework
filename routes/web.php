<?php

// Web Routes

use App\Controllers\CategoriesController;
use App\Controllers\HomeController;
use App\Controllers\LostItemsController;
use App\Controllers\StationsController;
use App\Controllers\UserController;
use App\Extensions\Route;


// Home routes
Route::get('/', [HomeController::class, 'showHomePage']);