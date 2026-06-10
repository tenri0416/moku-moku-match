@extends('layouts.app')

@section('content')
@php
    $isEdit = false;
    $formRoute = route('trainings.vocabulary.store');
    $formMethod = 'POST';
    $word = null;
@endphp

@include('trainings.vocabulary.form')
@endsection
