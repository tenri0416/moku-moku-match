@extends('layouts.app')

@section('title', '管理者ログイン認証')

@section('content')
<div class="min-h-screen bg-slate-50 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <h1 class="text-2xl font-bold text-slate-900">
            管理者ログイン認証
        </h1>

        <p class="mt-3 text-sm leading-6 text-slate-600">
            管理者メールアドレス宛に送信された6桁の認証コードを入力してください。
            有効期限は10分です。
        </p>

        @if (session('status'))
            <div class="mt-5 rounded-lg bg-green-50 p-4 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-5 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.verify.store') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="code" class="block text-sm font-bold text-slate-700">
                    認証コード
                </label>

                <input
                    id="code"
                    name="code"
                    type="text"
                    inputmode="numeric"
                    maxlength="6"
                    required
                    autofocus
                    class="mt-2 block w-full rounded-lg border border-slate-300 px-4 py-3 text-center text-2xl font-bold tracking-widest shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="123456"
                >
            </div>

            <button
                type="submit"
                class="flex w-full justify-center rounded-lg bg-slate-900 px-4 py-3 text-sm font-bold text-white hover:bg-slate-700"
            >
                認証してログイン
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('admin.login') }}" class="text-sm font-bold text-slate-600 hover:text-slate-900">
                ログイン画面に戻る
            </a>
        </div>
    </div>
</div>
@endsection
