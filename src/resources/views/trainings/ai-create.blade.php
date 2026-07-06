@extends('layouts.app')

@section('content')
@php
    if (!isset($scoreLabels)) {
        $scoreLabels = [];
    }

    $questionTitle = preg_replace('/^[\s　]+|[\s　]+$/u', '', $training->question_title ?? '');

    $questionBody = collect(preg_split("/\r\n|\n|\r/", $training->question_body ?? ''))
        ->map(function ($line) {
            // 前後の半角スペース・全角スペース・タブを削除
            $line = preg_replace('/^[\s　]+|[\s　]+$/u', '', $line);

            // 文中の連続スペース・タブ・全角スペースを1つにする
            $line = preg_replace('/[ \t　]+/u', ' ', $line);

            return $line;
        })
        ->filter(fn ($line) => $line !== '')
        ->implode(PHP_EOL);

    $questionBody = trim($questionBody);

    $isSummaryTraining = str_contains($typeLabel ?? '', '要約');
    $isVerbalizationTraining = str_contains($typeLabel ?? '', '言語化');

    $answerMaxLength = $isVerbalizationTraining ? 300 : 150;

    $trainingThemeLabel = $isSummaryTraining ? '要約力を高めるコツ' : '言語化力を高めるコツ';

    $tips = $isSummaryTraining
        ? [
            '重要な要点を残す',
            '短くても意味が伝わるように書く',
            '主題→要点→結論でまとめる',
        ]
        : [
            '結論だけでなく理由も添える',
            '感じたことを具体的な言葉で表す',
            '相手に伝わる順番で書く',
        ];

    $supportMessageTitle = $isSummaryTraining
        ? '短い文章でも、伝わる力は大きな武器になります！'
        : '言葉にすることで、考えが整理され、伝わる力が育ちます！';

    $supportMessageBody = '今日も一歩ずつ、あなたの言葉を磨いていきましょう。';
@endphp

@include('trainings.ai-create_sp')
@include('trainings.ai-create_pc')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textareas = document.querySelectorAll('[data-ai-answer-textarea]');

        textareas.forEach(function (textarea) {
            const targetId = textarea.dataset.countTarget;
            const counter = document.getElementById(targetId);

            if (!counter) {
                return;
            }

            const updateCount = function () {
                counter.textContent = textarea.value.length;
            };

            updateCount();
            textarea.addEventListener('input', updateCount);
        });
    });
</script>
@endsection
