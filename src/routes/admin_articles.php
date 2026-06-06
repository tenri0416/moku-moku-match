<?php

use App\Http\Controllers\Admin\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ArticleCategoryController;
use App\Http\Controllers\Admin\ArticleTagController;
use App\Http\Controllers\Admin\ArticleViewController;
use App\Http\Controllers\Admin\ArticleInquiryController as AdminArticleInquiryController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {
        Route::resource('articles', ArticleController::class);
        Route::resource('article-categories', ArticleCategoryController::class)
            ->except(['show']);

        Route::resource('article-tags', ArticleTagController::class)
            ->except(['show']);
        Route::get('/article-views', [ArticleViewController::class, 'index'])
            ->name('article-views.index');
    });

    Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {
        Route::get('/article-inquiries', [AdminArticleInquiryController::class, 'index'])
            ->name('article-inquiries.index');

        Route::get('/article-inquiries/{articleInquiry}', [AdminArticleInquiryController::class, 'show'])
            ->name('article-inquiries.show');

        Route::post('/article-inquiries/{articleInquiry}/reply', [AdminArticleInquiryController::class, 'reply'])
            ->name('article-inquiries.reply');
    });
