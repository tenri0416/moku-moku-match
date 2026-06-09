@extends('layouts.app')

@section('content')
@php
    $normalizeText = function (?string $value): string {
        return collect(preg_split("/\r\n|\n|\r/", $value ?? ''))
            ->map(fn ($line) => trim($line))
            ->filter(fn ($line) => $line !== '')
            ->implode(PHP_EOL);
    };

    $typeLabel = '想像力トレーニング';

    $questionBody = $normalizeText($training->question_body ?? '');
    $answerBody = $normalizeText($training->answer_body ?? '');
    $modelAnswer = $normalizeText($training->model_answer ?? '');
    $alternativeAnswer = $normalizeText($training->alternative_answer ?? '');
    $answerPoint = $normalizeText($training->answer_point ?? '');

    $totalScore = $training->total_score;
    $earnedPoints = $training->earned_points ?? 0;

    $scoreRows = [
        [
            'label' => '想像の広がり',
            'score' => $training->imagination_score,
            'icon' => '🌈',
        ],
        [
            'label' => '理由の納得感',
            'score' => $training->reason_score,
            'icon' => '💡',
        ],
        [
            'label' => '相手目線',
            'score' => $training->perspective_score,
            'icon' => '👀',
        ],
        [
            'label' => '表現のわかりやすさ',
            'score' => $training->expression_score,
            'icon' => '✏️',
        ],
    ];

    $scoreMessage = '今日も一歩前進です。';

    if ($totalScore !== null) {
        if ($totalScore >= 90) {
            $scoreMessage = 'とても良い想像力です。発想がよく広がっています！';
        } elseif ($totalScore >= 80) {
            $scoreMessage = 'よくできています。状況を自然に想像できています！';
        } elseif ($totalScore >= 70) {
            $scoreMessage = '良い回答です。次は別の可能性も足してみましょう！';
        } elseif ($totalScore >= 60) {
            $scoreMessage = 'しっかり取り組めています。理由を添えるとさらに良くなります！';
        }
    }

    $continuousDays = $continuousDays ?? 0;
    $monthlyRank = $monthlyRank ?? '-';
    $myTotalPoints = $myTotalPoints ?? 0;
@endphp

@include('trainings.imagination.show_sp')
@include('trainings.imagination.show_pc')
@endsection
