@extends('layouts.app')

@section('content')
@php
    $scoreLabels = $training->scoreLabels();

    $normalizeText = function (?string $value): string {
        return collect(preg_split("/\r\n|\n|\r/", $value ?? ''))
            ->map(fn ($line) => trim($line))
            ->filter(fn ($line) => $line !== '')
            ->implode(PHP_EOL);
    };

    $questionTitle = trim($training->question_title ?? '');
    $questionBody = $normalizeText($training->question_body ?? '');
    $answerBody = $normalizeText($training->answer_body ?? '');
    $diaryBody = $normalizeText($training->diary_body ?? '');

    $isAiQuestionTraining = in_array($type, ['summary', 'verbalization', 'abstraction', 'concretization'], true);

    $totalScore = $training->total_score;
    $earnedPoints = $training->earned_points ?? 0;

    $scoreRows = [
        [
            'label' => $scoreLabels['readability_score'] ?? '読みやすさ',
            'score' => $training->readability_score,
            'icon' => '📖',
        ],
        [
            'label' => $scoreLabels['specificity_score'] ?? '具体性',
            'score' => $training->specificity_score,
            'icon' => '💡',
        ],
        [
            'label' => $scoreLabels['structure_score'] ?? '構成',
            'score' => $training->structure_score,
            'icon' => '🔷',
        ],
        [
            'label' => $scoreLabels['expression_score'] ?? '表現力',
            'score' => $training->expression_score,
            'icon' => '✏️',
        ],
    ];

    $scoreMessage = '今日も一歩前進です。';

    if ($totalScore !== null) {
        if ($totalScore >= 90) {
            $scoreMessage = 'とても良い内容です。かなり成長しています！';
        } elseif ($totalScore >= 80) {
            $scoreMessage = 'よくできています。安定して力がついています！';
        } elseif ($totalScore >= 70) {
            $scoreMessage = '良い振り返りです。次は少し具体性を足してみましょう！';
        } elseif ($totalScore >= 60) {
            $scoreMessage = 'しっかり取り組めています。続けることが成長につながります！';
        }
    }

    // 現時点では画面崩れを避けるため固定値表示です。
    // 後でController側から渡す場合は、同名変数をcompactしてください。
    $continuousDays = $continuousDays ?? 7;
    $monthlyRank = $monthlyRank ?? 12;
    $totalPoints = $totalPoints ?? 1280;
@endphp

@include('trainings._score-result-modal')

@include('trainings.show_sp')
@include('trainings.show_pc')
@endsection
