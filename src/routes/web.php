<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDatabaseController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminWorkPostController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WorkPostController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminLogController;
/*
|--------------------------------------------------------------------------
| 公開ページ
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('/work-posts', [WorkPostController::class, 'index'])
    ->name('work-posts.index');

/*
|--------------------------------------------------------------------------
| メール認証関連
|--------------------------------------------------------------------------
| 新規登録直後には強制しない。
| verified が必要な機能にアクセスした時だけ、この画面へ誘導される。
*/

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('mypage')
        ->with('status', 'メールアドレスの認証が完了しました。');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', '認証メールを再送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| ログイン済みユーザー用ページ
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('mypage');
    })->name('dashboard');

    Route::get('/mypage', [MyPageController::class, 'index'])
        ->name('mypage');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update.patch');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | 閲覧系
    |--------------------------------------------------------------------------
    | ログインしていれば閲覧できる。
    | メール未認証でも見れる。
    */

    Route::get('/work-posts/{workPost}/applications', [ApplicationController::class, 'index'])
        ->whereNumber('workPost')
        ->name('applications.index');

    Route::get('/messages', [MessageController::class, 'index'])
        ->name('messages.index');

    Route::get('/messages/{workPost}/{user}', [MessageController::class, 'show'])
        ->whereNumber('workPost')
        ->whereNumber('user')
        ->name('messages.show');

    /*
    |--------------------------------------------------------------------------
    | メール認証済みユーザーだけ利用できる機能
    |--------------------------------------------------------------------------
    | 募集作成・編集・応募・メッセージ送信・通報・ブロックなど、
    | 他ユーザーに影響する操作だけメール認証を必須にする。
    */

    Route::middleware('verified')->group(function () {
        Route::get('/work-posts/create', [WorkPostController::class, 'create'])
            ->name('work-posts.create');

        Route::post('/work-posts', [WorkPostController::class, 'store'])
            ->name('work-posts.store');

        Route::get('/work-posts/{workPost}/edit', [WorkPostController::class, 'edit'])
            ->whereNumber('workPost')
            ->name('work-posts.edit');

        Route::put('/work-posts/{workPost}', [WorkPostController::class, 'update'])
            ->whereNumber('workPost')
            ->name('work-posts.update');

        Route::patch('/work-posts/{workPost}/close', [WorkPostController::class, 'close'])
            ->whereNumber('workPost')
            ->name('work-posts.close');

        Route::get('/work-posts/{workPost}/applications/create', [ApplicationController::class, 'create'])
            ->whereNumber('workPost')
            ->name('applications.create');

        Route::post('/work-posts/{workPost}/applications', [ApplicationController::class, 'store'])
            ->whereNumber('workPost')
            ->name('applications.store');

        Route::patch('/applications/{application}/approve', [ApplicationController::class, 'approve'])
            ->whereNumber('application')
            ->name('applications.approve');

        Route::patch('/applications/{application}/reject', [ApplicationController::class, 'reject'])
            ->whereNumber('application')
            ->name('applications.reject');

        Route::post('/messages/{workPost}/{user}', [MessageController::class, 'store'])
            ->whereNumber('workPost')
            ->whereNumber('user')
            ->name('messages.store');

        Route::get('/reports/create', [ReportController::class, 'create'])
            ->name('reports.create');

        Route::post('/reports', [ReportController::class, 'store'])
            ->name('reports.store');

        Route::post('/users/{user}/blocks', [BlockController::class, 'store'])
            ->whereNumber('user')
            ->name('blocks.store');

        Route::delete('/users/{user}/blocks', [BlockController::class, 'destroy'])
            ->whereNumber('user')
            ->name('blocks.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| 募集詳細
|--------------------------------------------------------------------------
| /work-posts/create を {workPost} と誤判定しないように、
| create/edit/update などの定義より後ろに置く。
*/

Route::get('/work-posts/{workPost}', [WorkPostController::class, 'show'])
    ->whereNumber('workPost')
    ->name('work-posts.show');

/*
|--------------------------------------------------------------------------
| 管理者ログイン
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| 管理者画面
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/users', [AdminUserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/{user}', [AdminUserController::class, 'show'])
            ->whereNumber('user')
            ->name('users.show');

        Route::patch('/users/{user}/suspend', [AdminDashboardController::class, 'suspend'])
            ->whereNumber('user')
            ->name('users.suspend');

        Route::patch('/users/{user}/activate', [AdminDashboardController::class, 'activate'])
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
            ->where('file', 'laravel(\-\d{4}\-\d{2}\-\d{2})?\.log')
            ->name('logs.show');
    });

/*
|--------------------------------------------------------------------------
| SEO記事
|--------------------------------------------------------------------------
*/

Route::prefix('site')->group(function () {
    Route::view('/remote-work-loneliness', 'articles.remote-work-loneliness')
        ->name('articles.remote-work-loneliness');

    Route::view('/freelance-loneliness', 'articles.freelance-loneliness')
        ->name('articles.freelance-loneliness');

    Route::view('/online-mokumoku', 'articles.online-mokumoku')
        ->name('articles.online-mokumoku');

    Route::view('/study-partner-online', 'articles.study-partner-online')
        ->name('articles.study-partner-online');

    Route::view('/work-alone-routine', 'articles.work-alone-routine')
        ->name('articles.work-alone-routine');

    Route::view('/remote-work-friends', 'articles.remote-work-friends')
        ->name('articles.remote-work-friends');
});

/*
|--------------------------------------------------------------------------
| サイトマップ
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', function () {
    $workPosts = \App\Models\WorkPost::query()
        ->where('status', '!=', \App\Models\WorkPost::STATUS_PRIVATE)
        ->latest('updated_at')
        ->get();

    return response()
        ->view('sitemap', compact('workPosts'))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

/*
|--------------------------------------------------------------------------
| 通常ユーザー認証ルート
|--------------------------------------------------------------------------
| Breeze等のログイン・登録・パスワードリセット用。
| このファイル内で dashboard / profile を定義しているため、
| auth.php 側で同じルートがある場合は重複に注意。
*/

require __DIR__ . '/auth.php';
require __DIR__ . '/articles.php';
require __DIR__ . '/admin_articles.php';

