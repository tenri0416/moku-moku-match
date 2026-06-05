@php
    $impersonation = session('admin_impersonation', []);
@endphp

@if (($impersonation['active'] ?? false) === true)
    {{-- PC版 --}}
    <div class="hidden md:block fixed left-0 top-28 z-[9999] w-56 rounded-r-2xl bg-rose-700 p-4 text-white shadow-2xl">
        <details>
            <summary class="cursor-pointer text-sm font-black leading-6">
                管理者でログイン中
            </summary>

            <div class="mt-3 space-y-3 text-xs leading-6">
                <div class="rounded-xl bg-white/10 p-3">
                    <p class="font-bold">操作対象ユーザー</p>
                    <p class="mt-1 break-words">
                        {{ $impersonation['user_name'] ?? 'ユーザー' }}
                    </p>
                </div>

                <form method="POST" action="{{ route('impersonation.stop') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-white px-3 py-2 text-xs font-black text-rose-700 shadow-sm transition hover:bg-rose-50"
                        onclick="return confirm('代理ログインを終了して管理者画面へ戻ります。よろしいですか？');"
                    >
                        代理ログインを終了
                    </button>
                </form>
            </div>
        </details>
    </div>

    {{-- SP版 --}}
    <div class="md:hidden fixed right-3 bottom-20 z-[9999]">
        <details class="group">
            <summary
                class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-full bg-rose-700 text-[11px] font-black text-white shadow-xl"
                aria-label="管理者代理ログイン中"
            >
                管理
            </summary>

            <div class="absolute bottom-14 right-0 w-56 rounded-2xl bg-rose-700 p-3 text-white shadow-2xl">
                <p class="text-[11px] font-black">
                    管理者でログイン中
                </p>

                <div class="mt-2 rounded-xl bg-white/10 p-2">
                    <p class="text-[10px] font-bold text-rose-100">操作対象ユーザー</p>
                    <p class="mt-1 break-words text-[11px] font-bold leading-5">
                        {{ $impersonation['user_name'] ?? 'ユーザー' }}
                    </p>
                </div>

                <form method="POST" action="{{ route('impersonation.stop') }}" class="mt-2">
                    @csrf

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-white px-3 py-2 text-[11px] font-black text-rose-700 shadow-sm"
                        onclick="return confirm('代理ログインを終了して管理者画面へ戻ります。よろしいですか？');"
                    >
                        終了して管理画面へ戻る
                    </button>
                </form>
            </div>
        </details>
    </div>
@endif
