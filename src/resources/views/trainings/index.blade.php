@extends('layouts.app')

@section('content')
@php
    $completedTodayCount = collect($todayStatuses ?? [])->filter()->count();
    $totalTrainingCount = is_countable($trainings ?? []) ? count($trainings) : 0;

    $continuousDays = $continuousDays ?? 0;
    $monthlyRank = $monthlyRank ?? '-';
    $historyCount = $historyCount ?? $totalTrainingCount;

    $historyLimit = request('history') === 'all' ? 10 : 3;
@endphp

@include('reading-reflection-trainings._modal')
@include('trainings.vocabulary._modal')
@include('trainings.index_sp')
@include('trainings.index_pc')
@endsection
