@extends('layouts.app')

@section('title', 'Googleフォトから選択')

@section('content')
<div class="min-h-screen bg-slate-50 px-4 py-10">
    <div class="mx-auto max-w-2xl">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <p class="text-sm font-bold text-blue-600">
                GOOGLE PHOTOS
            </p>

            <h1 class="mt-2 text-2xl font-black text-slate-900">
                Googleフォトからプロフィール画像を選択
            </h1>

            <p class="mt-3 text-sm leading-7 text-slate-600">
                Googleフォトの選択画面を開き、プロフィール画像にしたい写真を1枚選択してください。
                選択完了後、この画面に戻ると自動で保存できます。
            </p>

            <div id="google-photo-status" class="mt-6 rounded-2xl bg-blue-50 p-4 text-sm font-bold leading-7 text-blue-800">
                「Googleフォトを開く」ボタンを押してください。
            </div>

            <div class="mt-6 space-y-3">
                <button
                    type="button"
                    id="google-photo-open-button"
                    class="flex w-full items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-black text-white hover:bg-blue-700"
                >
                    Googleフォトを開く
                </button>

                <button
                    type="button"
                    id="google-photo-save-button"
                    class="hidden w-full items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-black text-white hover:bg-emerald-700"
                >
                    選択した画像を保存する
                </button>

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex w-full items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50"
                >
                    プロフィール編集へ戻る
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const openButton = document.getElementById('google-photo-open-button');
        const saveButton = document.getElementById('google-photo-save-button');
        const statusBox = document.getElementById('google-photo-status');

        let sessionId = null;
        let pickerWindow = null;
        let pollingTimer = null;
        let pollingStartedAt = null;

        const setStatus = (message, type = 'info') => {
            statusBox.textContent = message;

            statusBox.className = 'mt-6 rounded-2xl p-4 text-sm font-bold leading-7';

            if (type === 'success') {
                statusBox.classList.add('bg-emerald-50', 'text-emerald-800');
                return;
            }

            if (type === 'error') {
                statusBox.classList.add('bg-rose-50', 'text-rose-800');
                return;
            }

            statusBox.classList.add('bg-blue-50', 'text-blue-800');
        };

        const postJson = async (url, payload = {}) => {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(data.message || '処理に失敗しました。');
            }

            return data;
        };

        const getJson = async (url) => {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(data.message || '処理に失敗しました。');
            }

            return data;
        };

        const startPolling = () => {
            pollingStartedAt = Date.now();

            pollingTimer = setInterval(async () => {
                if (!sessionId) {
                    return;
                }

                if (Date.now() - pollingStartedAt > 10 * 60 * 1000) {
                    clearInterval(pollingTimer);
                    setStatus('選択待ちがタイムアウトしました。もう一度お試しください。', 'error');
                    openButton.disabled = false;
                    return;
                }

                try {
                    const session = await getJson(`{{ url('/profile/avatar/google/session') }}/${encodeURIComponent(sessionId)}`);

                    if (session.mediaItemsSet === true) {
                        clearInterval(pollingTimer);

                        setStatus('画像の選択が完了しました。「選択した画像を保存する」を押してください。', 'success');

                        saveButton.classList.remove('hidden');
                        saveButton.classList.add('flex');
                        openButton.classList.add('hidden');
                    }
                } catch (error) {
                    clearInterval(pollingTimer);
                    setStatus(error.message, 'error');
                    openButton.disabled = false;
                }
            }, 3000);
        };

        openButton?.addEventListener('click', async () => {
            openButton.disabled = true;
            setStatus('Googleフォト選択画面を準備しています...');

            try {
                const data = await postJson(`{{ route('profile.avatar.google.session') }}`);

                sessionId = data.session_id;

                pickerWindow = window.open(data.picker_uri, '_blank');

                if (!pickerWindow) {
                    setStatus('ポップアップがブロックされました。ブラウザ設定でポップアップを許可してください。', 'error');
                    openButton.disabled = false;
                    return;
                }

                setStatus('Googleフォトで写真を1枚選択してください。選択完了までこの画面を閉じないでください。');

                startPolling();
            } catch (error) {
                setStatus(error.message, 'error');
                openButton.disabled = false;
            }
        });

        saveButton?.addEventListener('click', async () => {
            if (!sessionId) {
                setStatus('Googleフォト選択セッションがありません。もう一度お試しください。', 'error');
                return;
            }

            saveButton.disabled = true;
            setStatus('選択した画像を保存しています...');

            try {
                const data = await postJson(`{{ route('profile.avatar.google.save') }}`, {
                    session_id: sessionId,
                });

                setStatus(data.message || 'プロフィール画像を保存しました。', 'success');

                setTimeout(() => {
                    window.location.href = data.redirect_url || `{{ route('profile.edit') }}`;
                }, 900);
            } catch (error) {
                setStatus(error.message, 'error');
                saveButton.disabled = false;
            }
        });
    })();
</script>
@endsection
