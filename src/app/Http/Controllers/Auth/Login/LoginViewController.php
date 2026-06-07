<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Login;

use App\Http\Controllers\Controller;
use App\Support\ApiActionLogger;

class LoginViewController extends Controller
{
  /**
   * ログイン画面を表示する。
   * @return \Illuminate\View\View
   */
  public function __invoke(): \Illuminate\View\View
  {
    ApiActionLogger::info(
      'AuthenticatedSessionController::create',
      'ユーザーログイン画面にアクセス',
      [
        'ip' => request()->ip(),
      ]
    );
    return view('auth.login');
  }
}
