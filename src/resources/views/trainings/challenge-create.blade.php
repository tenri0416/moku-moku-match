@extends('layouts.app')

@section('content')
    @include('trainings.challenge-create_sp')
    @include('trainings.challenge-create_pc')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const textareas = document.querySelectorAll('[data-training-textarea]');

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
