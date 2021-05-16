<?php
/*
 *
 * Application start file
 *
 * */

use App\Extensions\Config;
use App\Extensions\Route;
use App\Middlewares\Middleware;

// Include autoload for Controllers, Extensions, etc.
require_once __DIR__.'/../app/autoload.php';

// get configs
(new Config());
// run middlewares
Middleware::run();
// run routes
Route::run();
