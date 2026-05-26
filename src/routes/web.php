<?php

// use App\Http\Controllers\ProfileController;
// use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WorkPostController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminWorkPostController;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('/work-posts', [WorkPostController::class, 'index'])->name('work-posts.index');
Route::get('/work-posts/create', [WorkPostController::class, 'create'])->name('work-posts.create');
Route::get('/work-posts/{workPost}', [WorkPostController::class, 'show'])->name('work-posts.show');
Route::get('/work-posts/{workPost}/edit', [WorkPostController::class, 'edit'])->name('work-posts.edit');
Route::middleware('auth')->group(function () {
    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/work-posts/create', [WorkPostController::class, 'create'])->name('work-posts.create');
    Route::post('/work-posts', [WorkPostController::class, 'store'])->name('work-posts.store');
    Route::get('/work-posts/{workPost}/edit', [WorkPostController::class, 'edit'])->name('work-posts.edit');
    Route::put('/work-posts/{workPost}', [WorkPostController::class, 'update'])->name('work-posts.update');
    Route::patch('/work-posts/{workPost}/close', [WorkPostController::class, 'close'])->name('work-posts.close');
    Route::get('/work-posts/{workPost}/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/work-posts/{workPost}/applications/create', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/work-posts/{workPost}/applications', [ApplicationController::class, 'store'])->name('applications.store');
    Route::patch('/applications/{application}/approve', [ApplicationController::class, 'approve'])->name('applications.approve');
    Route::patch('/applications/{application}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');

    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{workPost}/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{workPost}/{user}', [MessageController::class, 'store'])->name('messages.store');

    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    Route::post('/users/{user}/blocks', [BlockController::class, 'store'])->name('blocks.store');
    Route::delete('/users/{user}/blocks', [BlockController::class, 'destroy'])->name('blocks.destroy');
});


Route::prefix('admin')
    ->name('admin.')
    // ->middleware(['auth', 'admin'])
    // ->middleware()
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('/users/{user}/suspend', [AdminDashboardController::class, 'suspend'])->name('users.suspend');
        Route::patch('/users/{user}/activate', [AdminDashboardController::class, 'activate'])->name('users.activate');

        Route::get('/work-posts', [AdminWorkPostController::class, 'index'])->name('work-posts.index');
        Route::get('/work-posts/{workPost}', [AdminWorkPostController::class, 'show'])->name('work-posts.show');
        Route::patch('/work-posts/{workPost}/private', [AdminWorkPostController::class, 'private'])->name('work-posts.private');
        Route::patch('/work-posts/{workPost}/open', [AdminWorkPostController::class, 'open'])->name('work-posts.open');

        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::patch('/reports/{report}/in-progress', [AdminReportController::class, 'inProgress'])->name('reports.in-progress');
        Route::patch('/reports/{report}/close', [AdminReportController::class, 'close'])->name('reports.close');
    });



    // 記事
    Route::view('/remote-work-loneliness', 'articles.remote-work-loneliness')
    ->name('articles.remote-work-loneliness');

    require __DIR__.'/auth.php';
