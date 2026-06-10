@extends('layouts.app')

@section('content')
@php
    $isEdit = true;
    $formRoute = route('trainings.vocabulary.update', $word);
    $formMethod = 'PUT';
@endphp

@include('trainings.vocabulary.form')
@endsection
