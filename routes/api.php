<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleController;

Route::get('articles/{article}', [ArticleController::class, 'show'])->name('api.v1.articles.show');
Route::get('articles', [ArticleController::class, 'index'])->name('api.v1.articles.index');
Route::post('articles', [ArticleController::class, 'store'])->name('api.v1.articles.store');