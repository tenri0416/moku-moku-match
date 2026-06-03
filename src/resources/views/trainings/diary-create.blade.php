@extends('layouts.app')

@section('content')
    @include('trainings.diary-create_sp')
    @include('trainings.diary-create_pc')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const textareas = document.querySelectorAll('[data-diary-textarea]');

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
