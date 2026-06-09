<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WorkPostController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\TrainingController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\HeaderStatusController;
use App\Http\Controllers\SatisfactionSurveyController;
use App\Http\Controllers\User\WithdrawalController;
use App\Http\Controllers\User\GooglePhotoAvatarController;
use App\Http\Controllers\ArticleInquiryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ReadingReflectionTrainingController;
use App\Http\Controllers\ConceptTrainingController;
use App\Http\Controllers\ImaginationTrainingController;



/*sa
|--------------------------------------------------------------------------
| 公開ページ
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('home');
    

Route::view('/privacy-policy', 'legal.privacy-policy')->name('privacy-policy');
Route::view('/terms', 'legal.terms')->name('terms');

Route::get('/work-posts', [WorkPostController::class, 'index'])
    ->name('work-posts.index');

    Route::get('/trainings/ranking', [TrainingController::class, 'ranking'])
    ->name('trainings.ranking');

    Route::get('/users/{user}', [UserProfileController::class, 'show'])
    ->name('users.show');


    Route::post('/article-inquiries', [ArticleInquiryController::class, 'store'])
    ->name('article-inquiries.store');

    Route::post('/articles/{article}/like', [ArticleController::class, 'like'])
    ->name('articles.like');


    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/trainings/reading-reflections', [ReadingReflectionTrainingController::class, 'index'])
            ->name('reading-reflections.index');
    
        Route::post('/trainings/reading-reflections', [ReadingReflectionTrainingController::class, 'store'])
            ->name('reading-reflections.store');
    });
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

    return redirect()
        ->route('mypage')
        ->with('status', 'メールアドレスの認証が完了しました。');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', '認証メールを再送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');




Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/trainings/imagination', [ImaginationTrainingController::class, 'create'])
        ->name('trainings.imagination.create');

    Route::post('/trainings/imagination', [ImaginationTrainingController::class, 'store'])
        ->name('trainings.imagination.store');

    Route::get('/trainings/imagination/{training}', [ImaginationTrainingController::class, 'show'])
        ->name('trainings.imagination.show');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile/avatar/google/redirect', [GooglePhotoAvatarController::class, 'redirect'])
        ->name('profile.avatar.google.redirect');

    Route::get('/profile/avatar/google/callback', [GooglePhotoAvatarController::class, 'callback'])
        ->name('profile.avatar.google.callback');

    Route::get('/profile/avatar/google/select', [GooglePhotoAvatarController::class, 'select'])
        ->name('profile.avatar.google.select');

    Route::post('/profile/avatar/google/session', [GooglePhotoAvatarController::class, 'createPickerSession'])
        ->name('profile.avatar.google.session');

    Route::get('/profile/avatar/google/session/{sessionId}', [GooglePhotoAvatarController::class, 'showPickerSession'])
        ->name('profile.avatar.google.session.show');

    Route::post('/profile/avatar/google/save', [GooglePhotoAvatarController::class, 'saveSelectedPhoto'])
        ->name('profile.avatar.google.save');
});




Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/trainings/concept', [ConceptTrainingController::class, 'create'])
        ->name('trainings.concept.create');

    Route::post('/trainings/concept', [ConceptTrainingController::class, 'store'])
        ->name('trainings.concept.store');

    Route::get('/trainings/concept/{training}', [ConceptTrainingController::class, 'show'])
        ->name('trainings.concept.show');
});
/*
|--------------------------------------------------------------------------
| ログイン済みユーザー用ページ
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('mypage');
    })->name('dashboard');

    Route::get('/header/status', [HeaderStatusController::class, 'show'])
    ->name('header.status');


    Route::get('/mypage', [MyPageController::class, 'index'])
        ->name('mypage');
       

    /*
    |--------------------------------------------------------------------------
    | プロフィール
    |--------------------------------------------------------------------------
    */

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update.patch');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    Route::post('/satisfaction-surveys', [SatisfactionSurveyController::class, 'store'])
        ->name('satisfaction-surveys.store');

    Route::post('/satisfaction-surveys/skip', [SatisfactionSurveyController::class, 'skip'])
        ->name('satisfaction-surveys.skip');
    /*
    |--------------------------------------------------------------------------
    | メッセージ
    |--------------------------------------------------------------------------
    | Ajaxポーリング対応のため、workPost と user をURLに含める。
    */

    Route::get('/messages', [MessageController::class, 'index'])
        ->name('messages.index');

    Route::get('/messages/{workPost}/{user}', [MessageController::class, 'show'])
        ->whereNumber('workPost')
        ->whereNumber('user')
        ->name('messages.show');

    Route::post('/messages/{workPost}/{user}', [MessageController::class, 'store'])
        ->whereNumber('workPost')
        ->whereNumber('user')
        ->name('messages.store');

    Route::get('/messages/{workPost}/{user}/latest', [MessageController::class, 'latest'])
        ->whereNumber('workPost')
        ->whereNumber('user')
        ->name('messages.latest');

    /*
    |--------------------------------------------------------------------------
    | 通知
    |--------------------------------------------------------------------------
    */

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/{id}', [NotificationController::class, 'show'])
        ->name('notifications.show');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');

    /*
    |--------------------------------------------------------------------------
    | 参加申請
    |--------------------------------------------------------------------------
    | Route::resource('applications') は使わない。
    | applications.create に workPost が必須なので、募集IDをURLに含める。
    */

    Route::get('/work-posts/{workPost}/applications', [ApplicationController::class, 'index'])
        ->whereNumber('workPost')
        ->name('applications.index');

        Route::get('/withdrawal', [WithdrawalController::class, 'edit'])
        ->name('withdrawal.edit');

    Route::delete('/withdrawal', [WithdrawalController::class, 'destroy'])
        ->name('withdrawal.destroy');
    /*
    |--------------------------------------------------------------------------
    | メール認証済みユーザーだけ利用できる機能
    |--------------------------------------------------------------------------
    | 募集作成・編集・応募・通報・ブロックなど、
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


            Route::get('/messages/users/{user}', [MessageController::class, 'showUser'])
            ->name('messages.users.show');
    
        Route::post('/messages/users/{user}', [MessageController::class, 'storeUser'])
            ->name('messages.users.store');
    
        Route::get('/messages/users/{user}/latest', [MessageController::class, 'latestUser'])
            ->name('messages.users.latest');

        Route::get('/messages/latest', [MessageController::class, 'latestIndex'])
            ->name('messages.index.latest');


        Route::post('/users/{user}/block', [BlockController::class, 'store'])
            ->name('users.block');
    
        Route::delete('/users/{user}/block', [BlockController::class, 'destroy'])
            ->name('users.unblock');
        
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('trainings')->name('trainings.')->group(function () {
        Route::get('/', [TrainingController::class, 'index'])->name('index');
        Route::get('/ranking', [TrainingController::class, 'ranking'])->name('ranking');

        Route::get('/diary/create', [TrainingController::class, 'createDiary'])->name('diary.create');
        Route::post('/diary', [TrainingController::class, 'storeDiary'])->name('diary.store');

        Route::get('/challenge/create', [TrainingController::class, 'createChallenge'])->name('challenge.create');
        Route::post('/challenge', [TrainingController::class, 'storeChallenge'])->name('challenge.store');

        Route::get('/summary/create', [TrainingController::class, 'createSummary'])->name('summary.create');
        Route::post('/summary/{training}', [TrainingController::class, 'storeSummary'])->name('summary.store');

        Route::get('/verbalization/create', [TrainingController::class, 'createVerbalization'])->name('verbalization.create');
        Route::post('/verbalization/{training}', [TrainingController::class, 'storeVerbalization'])->name('verbalization.store');

        Route::get('/abstraction/create', [TrainingController::class, 'createAbstraction'])->name('abstraction.create');
        Route::post('/abstraction/{training}', [TrainingController::class, 'storeAbstraction'])->name('abstraction.store');

        Route::get('/concretization/create', [TrainingController::class, 'createConcretization'])->name('concretization.create');
        Route::post('/concretization/{training}', [TrainingController::class, 'storeConcretization'])->name('concretization.store');

        Route::get('/{type}/{id}', [TrainingController::class, 'show'])->name('show');

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

    $articles = \App\Models\Article::query()
        ->where('status', \App\Models\Article::STATUS_PUBLIC)
        ->whereNotNull('published_at')
        ->where('published_at', '<=', now())
        ->latest('updated_at')
        ->get();

    return response()
        ->view('sitemap', compact('workPosts', 'articles'))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

/*
|--------------------------------------------------------------------------
| 通常ユーザー認証ルート
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| 記事ルート
|--------------------------------------------------------------------------
| 記事関連は分割ファイル側で管理する。
*/

require __DIR__ . '/articles.php';
require __DIR__ . '/admin_articles.php';
/*
|--------------------------------------------------------------------------
| 管理者ルート
|--------------------------------------------------------------------------
*/

require __DIR__ . '/admin.php';
