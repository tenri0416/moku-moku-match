<?php

use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;


Route::get('/articles', [ArticleController::class, 'index'])
    ->name('articles.index');
    Route::get('/articles/category/{category:slug}', [ArticleController::class, 'category'])
    ->name('articles.category');

Route::get('/articles/tag/{tag:slug}', [ArticleController::class, 'tag'])
    ->name('articles.tag');

Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])
    ->name('articles.show');

// /nara のような短縮SEO URL用
// 注意：何でも拾うため、必ず最後に書く
Route::get('/{shortSlug}', [ArticleController::class, 'showShort'])
    ->where('shortSlug', '[a-z0-9-]+')
    ->name('articles.short-show');
