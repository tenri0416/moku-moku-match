@extends('layouts.app')

@section('content')
@php
    $typeLabel = '想像力トレーニング';

    $scoreLabels = [
        '想像の広がり',
        '理由の納得感',
        '相手目線',
        '表現のわかりやすさ',
    ];

    $questionBody = trim($training->question_body ?? '');
    $answerMaxLength = 500;
    $storeRoute = route('trainings.imagination.store');

    $tips = [
        '状況・感情・理由をセットで考える',
        '1つに決めつけず別の可能性も考える',
        '相手の立場からも想像してみる',
    ];
@endphp

@include('trainings.imagination.create_sp')
@include('trainings.imagination.create_pc')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textareas = document.querySelectorAll('[data-ai-answer-textarea]');

        textareas.forEach(function (textarea) {
            const counter = document.getElementById(textarea.dataset.countTarget);

            if (!counter) {
                return;
            }

            const update = function () {
                counter.textContent = textarea.value.length;
            };

            update();
            textarea.addEventListener('input', update);
        });
    });
</script>
@endsection
