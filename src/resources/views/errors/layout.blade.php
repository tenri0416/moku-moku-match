@props([
    'code' => '500',
    'title' => 'エラーが発生しました',
    'message' => '申し訳ありません。処理中に問題が発生しました。',
    'detail' => null,
    'illustration' => '☕',
    'primaryLabel' => 'トップページへ戻る',
    'primaryUrl' => url('/'),
    'secondaryLabel' => 'マイページへ戻る',
    'secondaryUrl' => route('mypage'),
])

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }} | MokuMoku Match</title>
    <meta name="robots" content="noindex, nofollow">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#F8F5EF] text-gray-800">
    <main class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-2xl">
            <div class="bg-white rounded-3xl shadow-sm border border-orange-100 overflow-hidden">
                <div class="px-6 py-8 sm:px-10 sm:py-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-orange-50 text-4xl mb-6">
                        {{ $illustration }}
                    </div>

                    <p class="text-sm font-bold text-orange-500 tracking-widest mb-3">
                        ERROR {{ $code }}
                    </p>

                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-relaxed mb-4">
                        {{ $title }}
                    </h1>

                    <p class="text-gray-600 leading-8 mb-4">
                        {{ $message }}
                    </p>

                    @if ($detail)
                        <div class="mt-5 mb-7 rounded-2xl bg-orange-50 border border-orange-100 px-4 py-4 text-left">
                            <p class="text-sm text-gray-700 leading-7">
                                {{ $detail }}
                            </p>
                        </div>
                    @endif

                    <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ $primaryUrl }}"
                           class="inline-flex items-center justify-center rounded-full bg-orange-500 px-6 py-3 text-sm font-bold text-white shadow-sm hover:bg-orange-600 transition">
                            {{ $primaryLabel }}
                        </a>

                        @if ($secondaryUrl)
                            <a href="{{ $secondaryUrl }}"
                               class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-bold text-orange-600 border border-orange-200 hover:bg-orange-50 transition">
                                {{ $secondaryLabel }}
                            </a>
                        @endif
                    </div>

                    @auth
                        <div class="mt-5">
                            <a href="{{ route('mypage') }}"
                               class="text-sm text-gray-500 hover:text-orange-600 underline underline-offset-4">
                                マイページへ戻る
                            </a>
                        </div>
                    @endauth
                </div>

                <div class="bg-orange-50 px-6 py-4 text-center">
                    <p class="text-xs text-gray-500 leading-6">
                        作業仲間探し・学習仲間探しは、MokuMoku Match からいつでも再開できます。
                    </p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
