<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CategoryController;

Route::apiResource('articles', ArticleController::class);

Route::apiResource('categories', CategoryController::class)
        ->only(['index', 'show']);

Route::get('articles/{article}/category', fn () => 'TODO')
        ->name('articles.category');

Route::get('articles/{article}/relationships/category', fn () => 'TODO')
        ->name('articles.relationships.category');