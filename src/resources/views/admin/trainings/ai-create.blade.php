@extends('layouts.admin')

@section('content')
@php
    $questionBody = collect(preg_split("/\r\n|\n|\r/", $training->question_body ?? ''))
        ->map(fn ($line) => trim($line))
        ->implode(PHP_EOL);
@endphp

<div class="max-w-5xl mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ $typeLabel }}</h1>
        <p class="text-sm text-gray-500">
            AIが作成した問題に回答してください。回答後、AIが採点します。
        </p>
    </div>

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded shadow p-5 mb-6">
        <h2 class="text-lg font-bold mb-3">本日の問題</h2>

        <div class="mb-4">
            <h3 class="font-bold text-blue-700">{{ trim($training->question_title) }}</h3>
        </div>

        <div class="whitespace-pre-wrap leading-relaxed border rounded p-4 bg-gray-50">{{ $questionBody }}</div>
    </div>

    <form method="POST" action="{{ $storeRoute }}" data-ai-scoring-form class="bg-white rounded shadow p-5">
        @csrf

        <div class="mb-4">
            <label class="block font-bold mb-2">回答</label>
            <textarea
                name="answer_body"
                rows="12"
                class="w-full border rounded p-3"
                placeholder="ここに回答を入力してください。"
            >{{ old('answer_body', $training->answer_body) }}</textarea>

            @error('answer_body')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-5 bg-gray-50 border rounded p-4">
            <h3 class="font-bold mb-2">採点項目</h3>
            <ul class="list-disc pl-5 text-sm text-gray-700">
                @foreach ($scoreLabels as $label)
                    <li>{{ $label }}：25点</li>
                @endforeach
            </ul>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded">
                保存してAI採点する
            </button>

            <a href="{{ route('admin.trainings.index') }}" class="px-5 py-2 border rounded">
                一覧に戻る
            </a>
        </div>
    </form>
</div>
@endsection
