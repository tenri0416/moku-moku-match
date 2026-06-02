@php
    $isHome = request()->routeIs('home') || request()->path() === '/';
    $isTraining = request()->is('trainings*');
    $isArticle = request()->is('articles*') || request()->is('site*');

    // 管理者画面、ログイン画面、会員登録画面では表示しない
    $shouldHideMobileFooter =
        request()->is('admin*') ||
        request()->is('login') ||
        request()->is('register') ||
        request()->is('password*');
@endphp

@if (! $shouldHideMobileFooter)
    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 backdrop-blur md:hidden">
        <div class="grid grid-cols-3 pb-[env(safe-area-inset-bottom)]">
            {{-- 募集を検索 --}}
            <a
                href="{{ route('home') }}"
                class="flex flex-col items-center justify-center gap-1 px-2 py-2 transition
                    {{ $isHome ? 'bg-emerald-50 text-emerald-600' : 'text-slate-600 hover:bg-slate-50' }}"
            >
                <div class="flex h-8 w-8 items-center justify-center rounded-full
                    {{ $isHome ? 'bg-emerald-100' : 'bg-slate-100' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 10.5L12 3l9 7.5" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M5.25 9.75V21h13.5V9.75" />
                    </svg>
                </div>

                <span class="text-[11px] font-bold leading-tight">
                    募集を検索
                </span>
            </a>

            {{-- トレーニングする --}}
            <a
                href="{{ url('/trainings') }}"
                class="flex flex-col items-center justify-center gap-1 px-2 py-2 transition
                    {{ $isTraining ? 'bg-teal-50 text-teal-600' : 'text-slate-600 hover:bg-slate-50' }}"
            >
                <div class="flex h-8 w-8 items-center justify-center rounded-full
                    {{ $isTraining ? 'bg-teal-100' : 'bg-slate-100' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 6v12m6-6H6" />
                    </svg>
                </div>

                <span class="text-[11px] font-bold leading-tight">
                    トレーニングする
                </span>
            </a>

            {{-- 記事 --}}
            <a
                href="{{ url('/articles') }}"
                class="flex flex-col items-center justify-center gap-1 px-2 py-2 transition
                    {{ $isArticle ? 'bg-cyan-50 text-cyan-700' : 'text-slate-600 hover:bg-slate-50' }}"
            >
                <div class="flex h-8 w-8 items-center justify-center rounded-full
                    {{ $isArticle ? 'bg-cyan-100' : 'bg-slate-100' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M19.5 5.25h-15a1.5 1.5 0 00-1.5 1.5v10.5a1.5 1.5 0 001.5 1.5h15a1.5 1.5 0 001.5-1.5V6.75a1.5 1.5 0 00-1.5-1.5z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M7.5 9h9M7.5 12h9M7.5 15h6" />
                    </svg>
                </div>

                <span class="text-[11px] font-bold leading-tight">
                    記事
                </span>
            </a>
        </div>
    </nav>
@endif
