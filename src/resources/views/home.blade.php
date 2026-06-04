@extends('layouts.app')

@section('title', 'MokuMoku Match')

@section('content')
@include('home_sp')
@include('home_pc')
{{-- 初回案内モーダル --}}
@include('home._guest-onboarding-modal')

{{-- 満足度調査アンケートモーダル --}}
@include('home._satisfaction-survey-modal')
@endsection
