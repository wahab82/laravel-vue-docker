<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-search' , [\App\Http\Controllers\PostController::class, 'search']);
