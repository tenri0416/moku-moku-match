@csrf

<div class="space-y-6">
    @if (isset($previousWorkPost))
    <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4">
        <label class="flex cursor-pointer items-center gap-3">
            <input
                type="checkbox"
                id="use-previous-post"
                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
            >

            <span class="text-sm font-bold text-slate-700">
                前回のタイトル・募集内容を引き継ぐ
            </span>
        </label>

        <p class="mt-2 text-xs text-slate-500">
            チェックすると、前回入力したタイトルと募集内容が自動で入力されます。
        </p>
    </div>
@endif
    {{-- タイトル --}}
    <div>
        <label for="title" class="mb-2 block text-sm font-bold text-slate-700">
            タイトル <span class="text-rose-500">必須</span>
        </label>

        <input type="text" id="title" name="title" value="{{ old('title', $workPost->title ?? '') }}"
            placeholder="例：平日午前に一緒に黙々作業できる方募集"
            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

        @error('title')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- 開始日時 --}}
    <div>
        <label for="start_at" class="mb-2 block text-sm font-bold text-slate-700">
            開始日時
        </label>

        <input type="datetime-local" id="start_at" name="start_at"
            value="{{ old('start_at', isset($workPost) && $workPost->start_at ? $workPost->start_at->format('Y-m-d\TH:i') : '') }}"
            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

        @error('start_at')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- 終了日時 --}}
    <div>
        <label for="end_at" class="mb-2 block text-sm font-bold text-slate-700">
            終了日時
        </label>

        <input type="datetime-local" id="end_at" name="end_at"
            value="{{ old('end_at', isset($workPost) && $workPost->end_at ? $workPost->end_at->format('Y-m-d\TH:i') : '') }}"
            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

        @error('end_at')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="meeting_url" class="mb-2 block text-sm font-bold text-slate-700">
            URL
        </label>

        <input type="text" id="meeting_url" name="meeting_url"
            value="{{ old('meeting_url', $workPost->meeting_url ?? '') }}"
            placeholder="オンラインの場合はここにURLを入力して下さい。Zoom、Google Meet、Discord"
            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

        @error('meeting_url')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- 募集内容 --}}
    <div>
        <label for="body" class="mb-2 block text-sm font-bold text-slate-700">
            募集内容 <span class="text-rose-500">必須</span>
        </label>

        <textarea id="body" name="body" rows="5" placeholder="募集内容を入力してください。例：Zoomをつないで、最初と最後だけ会話し、作業中は黙々作業したいです。"
            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('body', $workPost->body ?? '') }}</textarea>

        @error('body')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>


    {{-- 詳細入力ボタン --}}
    <div>
        <button type="button" id="detail-toggle" aria-expanded="false" aria-controls="detail-fields"
            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">

            <span id="detail-toggle-icon">＋</span>
            <span id="detail-toggle-text">詳細を入力する</span>
        </button>
    </div>


    {{-- 任意入力項目 --}}
    <div id="detail-fields" class="hidden">
        <div class="grid gap-6 md:grid-cols-2">

            {{-- 目的 --}}
            <div>
                <label for="purpose" class="mb-2 block text-sm font-bold text-slate-700">
                    目的
                </label>

                <input type="text" id="purpose" name="purpose"
                    value="{{ old('purpose', $workPost->purpose ?? '勉強') }}" placeholder="例：黙々作業、勉強、情報交換"
                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                @error('purpose')
                    <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>


            {{-- 開催形式 --}}
            <div>
                <label for="location_type" class="mb-2 block text-sm font-bold text-slate-700">
                    開催形式
                </label>

                <select id="location_type" name="location_type"
                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <option value="online" @selected(old('location_type', $workPost->location_type ?? '') === 'online')>
                        オンライン
                    </option>

                    <option value="offline" @selected(old('location_type', $workPost->location_type ?? '') === 'offline')>
                        オフライン
                    </option>

                    <option value="both" @selected(old('location_type', $workPost->location_type ?? 'both') === 'both')>
                        どちらでも可
                    </option>
                </select>

                @error('location_type')
                    <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>


            {{-- 使用ツール --}}
            <div>
                <label for="meeting_tool" class="mb-2 block text-sm font-bold text-slate-700">
                    使用ツール
                </label>

                <input type="text" id="meeting_tool" name="meeting_tool"
                    value="{{ old('meeting_tool', $workPost->meeting_tool ?? '') }}"
                    placeholder="例：Zoom、Google Meet、Discord"
                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                @error('meeting_tool')
                    <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>


            {{-- 都道府県 --}}
            <div>
                <label for="prefecture_id" class="mb-2 block text-sm font-bold text-slate-700">
                    都道府県
                </label>

                <select id="prefecture_id" name="prefecture_id"
                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <option value="">選択してください</option>

                    @foreach ($prefectures as $prefecture)
                        <option value="{{ $prefecture->id }}" @selected((string) old('prefecture_id', $workPost->prefecture_id ?? '') === (string) $prefecture->id)>
                            {{ $prefecture->name }}
                        </option>
                    @endforeach
                </select>

                @error('prefecture_id')
                    <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>


            {{-- 時間帯 --}}
            <div>
                <label for="time_zone" class="mb-2 block text-sm font-bold text-slate-700">
                    時間帯
                </label>

                <select id="time_zone" name="time_zone"
                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <option value="">選択してください</option>

                    <option value="morning" @selected(old('time_zone', $workPost->time_zone ?? '') === 'morning')>
                        朝
                    </option>

                    <option value="daytime" @selected(old('time_zone', $workPost->time_zone ?? '') === 'daytime')>
                        昼
                    </option>

                    <option value="night" @selected(old('time_zone', $workPost->time_zone ?? '') === 'night')>
                        夜
                    </option>
                </select>

                @error('time_zone')
                    <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>


            {{-- 募集人数 --}}
            <div>
                <label for="max_participants" class="mb-2 block text-sm font-bold text-slate-700">
                    募集人数
                </label>

                <input type="number" id="max_participants" name="max_participants"
                    value="{{ old('max_participants', $workPost->max_participants ?? '') }}" min="1"
                    placeholder="例：2"
                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                @error('max_participants')
                    <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>

        </div>
    </div>


    {{-- ボタン --}}
    <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
        <button type="submit"
            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700">
            保存する
        </button>

        <a href="{{ route('home') }}"
            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
            募集一覧へ戻る
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    
            /*
             * 詳細入力の開閉
             */
            const toggleButton = document.getElementById('detail-toggle');
            const detailFields = document.getElementById('detail-fields');
            const toggleText = document.getElementById('detail-toggle-text');
            const toggleIcon = document.getElementById('detail-toggle-icon');
    
            toggleButton.addEventListener('click', function() {
                const isHidden = detailFields.classList.toggle('hidden');
    
                toggleButton.setAttribute('aria-expanded', !isHidden);
    
                if (isHidden) {
                    toggleText.textContent = '詳細を入力する';
                    toggleIcon.textContent = '＋';
                } else {
                    toggleText.textContent = '詳細を閉じる';
                    toggleIcon.textContent = '－';
                }
            });
    
    
            /*
             * 前回の募集内容を引き継ぐ
             */
            const previousCheckbox = document.getElementById('use-previous-post');
    
            if (previousCheckbox) {
    
                const title = document.getElementById('title');
                const body = document.getElementById('body');
    
                const previousTitle = @json($previousWorkPost->title ?? '');
                const previousBody = @json($previousWorkPost->body ?? '');
    
                // チェックする前の値を保存
                let originalTitle = title.value;
                let originalBody = body.value;
    
                previousCheckbox.addEventListener('change', function() {
    
                    if (this.checked) {
    
                        // 現在入力している値を保存
                        originalTitle = title.value;
                        originalBody = body.value;
    
                        // 前回値を設定
                        title.value = previousTitle;
                        body.value = previousBody;
    
                    } else {
    
                        // チェック前の値に戻す
                        title.value = originalTitle;
                        body.value = originalBody;
    
                    }
    
                });
            }
    
        });
    </script>
