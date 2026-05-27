<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    public function index()
    {
        Log::info('管理者ログインページにアクセス');
        return view('admin.auth.login');
    }
}
