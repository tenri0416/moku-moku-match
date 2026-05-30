<?php

use App\Http\Controllers\Admin\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ArticleCategoryController;
use App\Http\Controllers\Admin\ArticleTagController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {
        Route::resource('articles', ArticleController::class);
        Route::resource('article-categories', ArticleCategoryController::class)
            ->except(['show']);

        Route::resource('article-tags', ArticleTagController::class)
            ->except(['show']);
    });
