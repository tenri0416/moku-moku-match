@csrf

<div class="space-y-6">
    {{-- タイトル --}}
    <div>
        <label for="title" class="mb-2 block text-sm font-bold text-slate-700">
            タイトル <span class="text-rose-500">必須</span>
        </label>

        <input
            type="text"
            id="title"
            name="title"
            value="{{ old('title', $workPost->title ?? '') }}"
            placeholder="例：平日午前に一緒に黙々作業できる方募集"
            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >

        @error('title')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- 募集内容 --}}
    <div>
        <label for="body" class="mb-2 block text-sm font-bold text-slate-700">
            募集内容 <span class="text-rose-500">必須</span>
        </label>

        <textarea
            id="body"
            name="body"
            rows="8"
            placeholder="募集内容を入力してください。例：Zoomをつないで、最初と最後だけ会話し、作業中は黙々作業したいです。"
            class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >{{ old('body', $workPost->body ?? '') }}</textarea>

        @error('body')
            <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        {{-- 目的 --}}
        <div>
            <label for="purpose" class="mb-2 block text-sm font-bold text-slate-700">
                目的 <span class="text-rose-500">必須</span>
            </label>

            <input
                type="text"
                id="purpose"
                name="purpose"
                value="{{ old('purpose', $workPost->purpose ?? '') }}"
                placeholder="例：黙々作業、勉強、情報交換"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >

            @error('purpose')
                <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- 開催形式 --}}
        <div>
            <label for="location_type" class="mb-2 block text-sm font-bold text-slate-700">
                開催形式 <span class="text-rose-500">必須</span>
            </label>

            <select
                id="location_type"
                name="location_type"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="online" @selected(old('location_type', $workPost->location_type ?? 'online') === 'online')>
                    オンライン
                </option>
                <option value="offline" @selected(old('location_type', $workPost->location_type ?? '') === 'offline')>
                    オフライン
                </option>
                <option value="both" @selected(old('location_type', $workPost->location_type ?? '') === 'both')>
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

            <input
                type="text"
                id="meeting_tool"
                name="meeting_tool"
                value="{{ old('meeting_tool', $workPost->meeting_tool ?? '') }}"
                placeholder="例：Zoom、Google Meet、Discord"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >

            @error('meeting_tool')
                <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- 都道府県 --}}
        <div>
            <label for="prefecture" class="mb-2 block text-sm font-bold text-slate-700">
                都道府県
            </label>

            <input
                type="text"
                id="prefecture"
                name="prefecture"
                value="{{ old('prefecture', $workPost->prefecture ?? '') }}"
                placeholder="例：奈良県"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >

            @error('prefecture')
                <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- 開始日時 --}}
        <div>
            <label for="start_at" class="mb-2 block text-sm font-bold text-slate-700">
                開始日時
            </label>

            <input
                type="datetime-local"
                id="start_at"
                name="start_at"
                value="{{ old('start_at', isset($workPost) && $workPost->start_at ? $workPost->start_at->format('Y-m-d\TH:i') : '') }}"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >

            @error('start_at')
                <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- 終了日時 --}}
        <div>
            <label for="end_at" class="mb-2 block text-sm font-bold text-slate-700">
                終了日時
            </label>

            <input
                type="datetime-local"
                id="end_at"
                name="end_at"
                value="{{ old('end_at', isset($workPost) && $workPost->end_at ? $workPost->end_at->format('Y-m-d\TH:i') : '') }}"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >

            @error('end_at')
                <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- 時間帯 --}}
        <div>
            <label for="time_zone" class="mb-2 block text-sm font-bold text-slate-700">
                時間帯
            </label>

            <select
                id="time_zone"
                name="time_zone"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
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

            <input
                type="number"
                id="max_participants"
                name="max_participants"
                value="{{ old('max_participants', $workPost->max_participants ?? '') }}"
                min="1"
                placeholder="例：2"
                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >

            @error('max_participants')
                <p class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- ボタン --}}
    <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700"
        >
            保存する
        </button>

        <a
            href="{{ route('work-posts.index') }}"
            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
        >
            募集一覧へ戻る
        </a>
    </div>
</div>
