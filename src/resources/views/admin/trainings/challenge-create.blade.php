@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-2">今日のチャレンジ</h1>
    <p class="text-sm text-gray-500 mb-6">今日チャレンジしたことを振り返り、次の改善につなげます。</p>

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.trainings.challenge.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block font-bold mb-1">日付</label>
            <input type="date" name="training_date" value="{{ old('training_date', now()->format('Y-m-d')) }}" class="w-full border rounded p-2">
            @error('training_date')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-bold mb-1">今日チャレンジしたこと</label>
            <textarea name="challenged_thing" rows="4" class="w-full border rounded p-3">{{ old('challenged_thing') }}</textarea>
            @error('challenged_thing')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-bold mb-1">できたこと</label>
            <textarea name="completed_thing" rows="4" class="w-full border rounded p-3">{{ old('completed_thing') }}</textarea>
            @error('completed_thing')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-bold mb-1">難しかったこと</label>
            <textarea name="difficult_thing" rows="4" class="w-full border rounded p-3">{{ old('difficult_thing') }}</textarea>
            @error('difficult_thing')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-bold mb-1">次に改善したいこと</label>
            <textarea name="next_improvement" rows="4" class="w-full border rounded p-3">{{ old('next_improvement') }}</textarea>
            @error('next_improvement')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2 bg-green-600 text-white rounded">
                保存して採点する
            </button>
            <a href="{{ route('admin.trainings.index') }}" class="px-5 py-2 border rounded">
                戻る
            </a>
        </div>
    </form>
</div>
@endsection
