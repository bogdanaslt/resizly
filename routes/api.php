<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/{parameters}/{image}', ImageController::class)
    ->where('image', '.*');
