@php
    $impersonation = session('admin_impersonation', []);
@endphp

@if (($impersonation['active'] ?? false) === true)
    <div class="fixed left-0 top-28 z-[9999] w-56 rounded-r-2xl bg-rose-700 p-4 text-white shadow-2xl">
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
@endif
