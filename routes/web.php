<?php

use Illuminate\Support\Facades\Route;
use ttungbmt\CorsProxy\Http\Controllers\CorsProxyController;

Route::any('proxy', CorsProxyController::class);
