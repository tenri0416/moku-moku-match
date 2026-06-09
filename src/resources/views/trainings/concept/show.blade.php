@extends('layouts.app')

@section('content')
@php
    $normalizeText = function (?string $value): string {
        return collect(preg_split("/\r\n|\n|\r/", $value ?? ''))
            ->map(function ($line) {
                $line = preg_replace('/^[\s　]+|[\s　]+$/u', '', $line);
                $line = preg_replace('/[ \t　]+/u', ' ', $line);

                return $line;
            })
            ->filter(fn ($line) => $line !== '')
            ->implode(PHP_EOL);
    };

    $typeLabel = '具体・抽象トレーニング';

    $questionTitle = $normalizeText($training->question_title ?? '具体・抽象トレーニング');
    $questionBody = $normalizeText($training->question_body ?? '');
    $answerBody = $normalizeText($training->answer_body ?? '');
    $modelAnswer = $normalizeText($training->model_answer ?? '');
    $alternativeAnswer = $normalizeText($training->alternative_answer ?? '');
    $answerPoint = $normalizeText($training->answer_point ?? '');

    $totalScore = $training->total_score;
    $earnedPoints = $training->earned_points ?? 0;

    $scoreRows = [
        [
            'label' => '共通点の発見',
            'score' => $training->common_point_score,
            'icon' => '🔍',
        ],
        [
            'label' => '本質の捉え方',
            'score' => $training->essence_score,
            'icon' => '💎',
        ],
        [
            'label' => '視点の面白さ',
            'score' => $training->viewpoint_score,
            'icon' => '🧠',
        ],
        [
            'label' => '理由の説明',
            'score' => $training->explanation_score,
            'icon' => '✏️',
        ],
    ];

    $scoreMessage = '今日も一歩前進です。';

    if ($totalScore !== null) {
        if ($totalScore >= 90) {
            $scoreMessage = 'とても良い視点です。本質をかなり捉えられています！';
        } elseif ($totalScore >= 80) {
            $scoreMessage = 'よくできています。共通点を見つける力が育っています！';
        } elseif ($totalScore >= 70) {
            $scoreMessage = '良い回答です。次はもう少し深い目的まで考えてみましょう！';
        } elseif ($totalScore >= 60) {
            $scoreMessage = 'しっかり取り組めています。続けることで抽象化力が育ちます！';
        }
    }

    $continuousDays = $continuousDays ?? 0;
    $monthlyRank = $monthlyRank ?? '-';
    $myTotalPoints = $myTotalPoints ?? 0;
@endphp

@include('trainings._score-result-modal')

@include('trainings.concept.show_sp')
@include('trainings.concept.show_pc')
@endsection
