@extends('layouts.app')

@section('content')
@php
    $trainingCards = [
        [
            'key' => 'diary',
            'label' => '日記トレーニング',
            'short_label' => '日記',
            'description' => '今日の出来事を書く',
            'pc_description' => '今日の出来事を<br>書いて振り返る',
            'route' => route('trainings.diary.create'),
            'points' => '最大10pt',
            'emoji' => '📝',
            'bg' => 'bg-blue-100',
            'loading' => true,
        ],
        [
            'key' => 'challenge',
            'label' => '今日のチャレンジ',
            'short_label' => '今日のチャレンジ',
            'description' => '挑戦を振り返る',
            'pc_description' => '挑戦を振り返り<br>気づきを得る',
            'route' => route('trainings.challenge.create'),
            'points' => '最大10pt',
            'emoji' => '🔥',
            'bg' => 'bg-orange-100',
            'loading' => true,
        ],
        [
            'key' => 'summary',
            'label' => '要約力',
            'short_label' => '要約力',
            'description' => '文章を短くまとめる',
            'pc_description' => '文章を短くまとめて<br>要点をつかむ',
            'route' => route('trainings.summary.create'),
            'points' => '最大10pt',
            'emoji' => '📖',
            'bg' => 'bg-purple-100',
            'loading' => false,
        ],
        [
            'key' => 'verbalization',
            'label' => '言語化力',
            'short_label' => '言語化力',
            'description' => '考えを言葉にする',
            'pc_description' => '考えを言葉にして<br>伝える力を鍛える',
            'route' => route('trainings.verbalization.create'),
            'points' => '最大10pt',
            'emoji' => '💬',
            'bg' => 'bg-emerald-100',
            'loading' => true,
        ],
        [
            'key' => 'abstraction',
            'label' => '抽象化力',
            'short_label' => '抽象化力',
            'description' => '本質を見つける',
            'pc_description' => '共通点を見つけて<br>本質を捉える',
            'route' => route('trainings.abstraction.create'),
            'points' => '最大10pt',
            'emoji' => '🧠',
            'bg' => 'bg-pink-100',
            'loading' => true,
        ],
        [
            'key' => 'concretization',
            'label' => '具体化力',
            'short_label' => '具体化力',
            'description' => '行動に落とし込む',
            'pc_description' => 'アイデアを行動に<br>落とし込む',
            'route' => route('trainings.concretization.create'),
            'points' => '最大10pt',
            'emoji' => '🎯',
            'bg' => 'bg-orange-100',
            'loading' => true,
        ],
    ];

    $completedTodayCount = collect($todayStatuses ?? [])->filter()->count();
    $totalTrainingCount = is_countable($trainings ?? []) ? count($trainings) : 0;

    $continuousDays = $continuousDays ?? 7;
    $monthlyRank = $monthlyRank ?? 12;
    $historyCount = $totalTrainingCount ?: 12;
@endphp

@include('trainings.index_sp')
@include('trainings.index_pc')
@endsection
