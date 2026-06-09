@extends('layouts.app')

@section('content')
@php
    $typeLabel = '具体・抽象トレーニング';

    $scoreLabels = [
        '共通点の発見',
        '本質の捉え方',
        '視点の面白さ',
        '理由の説明',
    ];

    $questionTitle = preg_replace('/^[\s　]+|[\s　]+$/u', '', $training->question_title ?? '具体・抽象トレーニング');

    $questionBody = collect(preg_split("/\r\n|\n|\r/", $training->question_body ?? ''))
        ->map(function ($line) {
            $line = preg_replace('/^[\s　]+|[\s　]+$/u', '', $line);
            $line = preg_replace('/[ \t　]+/u', ' ', $line);

            return $line;
        })
        ->filter(fn ($line) => $line !== '')
        ->implode(PHP_EOL);

    $questionBody = trim($questionBody);

    $answerMaxLength = 300;

    $trainingThemeLabel = '具体・抽象力を高めるコツ';

    $tips = [
        '役割・目的・機能の共通点を考える',
        '表面的な似ている点だけで終わらせない',
        '別の見方でも一緒と言えるか考える',
    ];

    $supportMessageTitle = '一見違うものをつなげる力は、考える力の土台になります！';
    $supportMessageBody = '今日も短い言葉で、本質を見つける練習をしていきましょう。';

    $storeRoute = route('trainings.concept.store');

    // 既存画面と変数名を合わせる。未定義でも落ちないようにする。
    $continuousDays = $continuousDays ?? 0;
    $myTotalPoints = $myTotalPoints ?? 0;
    $monthlyRank = $monthlyRank ?? '-';
@endphp

@include('trainings.concept.create_sp')
@include('trainings.concept.create_pc')

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
