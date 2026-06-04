<?php

use App\Http\Controllers\Admin\AiUsageDashboardController;
use App\Http\Controllers\Admin\ArticleViewController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDatabaseController;
use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminWorkPostController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/login', [AdminAuthController::class, 'index'])
    ->name('admin.login');

Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->name('admin.login.store');

Route::get('/admin/login/verify', [AdminAuthController::class, 'showVerify'])
    ->name('admin.login.verify');

Route::post('/admin/login/verify', [AdminAuthController::class, 'verify'])
    ->name('admin.login.verify.store');

Route::post('/admin/logout', [AdminAuthController::class, 'logout'])
    ->name('admin.logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/ai-usage', [AiUsageDashboardController::class, 'index'])
            ->name('ai-usage.index');

        Route::post('/notifications/mark-all-as-read', [AdminNotificationController::class, 'markAllAsRead'])
            ->name('notifications.mark-all-as-read');

        Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllAsRead'])
            ->name('notifications.read-all');

        Route::get('/users', [AdminUserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/{user}', [AdminUserController::class, 'show'])
            ->whereNumber('user')
            ->name('users.show');

        Route::patch('/users/{user}/suspend', [AdminUserController::class, 'suspend'])
            ->whereNumber('user')
            ->name('users.suspend');

        Route::patch('/users/{user}/activate', [AdminUserController::class, 'activate'])
            ->whereNumber('user')
            ->name('users.activate');

        Route::get('/work-posts', [AdminWorkPostController::class, 'index'])
            ->name('work-posts.index');

        Route::get('/work-posts/{workPost}', [AdminWorkPostController::class, 'show'])
            ->whereNumber('workPost')
            ->name('work-posts.show');

        Route::patch('/work-posts/{workPost}/private', [AdminWorkPostController::class, 'private'])
            ->whereNumber('workPost')
            ->name('work-posts.private');

        Route::patch('/work-posts/{workPost}/open', [AdminWorkPostController::class, 'open'])
            ->whereNumber('workPost')
            ->name('work-posts.open');

        Route::get('/reports', [AdminReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/reports/{report}', [AdminReportController::class, 'show'])
            ->whereNumber('report')
            ->name('reports.show');

        Route::patch('/reports/{report}/in-progress', [AdminReportController::class, 'inProgress'])
            ->whereNumber('report')
            ->name('reports.in-progress');

        Route::patch('/reports/{report}/close', [AdminReportController::class, 'close'])
            ->whereNumber('report')
            ->name('reports.close');

        Route::get('/database', [AdminDatabaseController::class, 'index'])
            ->name('database.index');

        Route::get('/database/{table}', [AdminDatabaseController::class, 'show'])
            ->where('table', '[A-Za-z0-9_]+')
            ->name('database.show');

        Route::get('/logs', [AdminLogController::class, 'index'])
            ->name('logs.index');

        Route::get('/logs/{file}', [AdminLogController::class, 'show'])
            ->where('file', '.*')
            ->name('logs.show');

        Route::get('/article-views', [ArticleViewController::class, 'index'])
            ->name('article-views.index');
    });
