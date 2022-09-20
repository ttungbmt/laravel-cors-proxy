<?php

use Illuminate\Support\Facades\Route;
use TungTT\CorsProxy\Http\Controllers\CorsProxyController;

Route::any('proxy', CorsProxyController::class);
Route::get('proxy/{path}', CorsProxyController::class)->where('path', '.*');

