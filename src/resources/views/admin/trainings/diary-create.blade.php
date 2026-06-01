@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-2">日記トレーニング</h1>
    <p class="text-sm text-gray-500 mb-6">今日の出来事・感情・理由・学びを書いてください。</p>

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.trainings.diary.store') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block font-bold mb-1">日付</label>
            <input type="date" name="training_date" value="{{ old('training_date', now()->format('Y-m-d')) }}" class="w-full border rounded p-2">
            @error('training_date')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block font-bold mb-1">日記</label>
            <textarea name="diary_body" rows="14" class="w-full border rounded p-3" placeholder="例：今日は〇〇をしました。最初は大変でしたが、〇〇に気づきました。">{{ old('diary_body') }}</textarea>
            @error('diary_body')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded">
                保存して採点する
            </button>
            <a href="{{ route('admin.trainings.index') }}" class="px-5 py-2 border rounded">
                戻る
            </a>
        </div>
    </form>
</div>
@endsection
