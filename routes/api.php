<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;

Route::apiResource('articles', ArticleController::class);

Route::apiResource('categories', CategoryController::class)
        ->only(['index', 'show']);