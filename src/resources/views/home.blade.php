@extends('layouts.app')

@section('title', 'MokuMoku Match')

@section('content')
@include('home_sp')
@include('home_pc')
@include('home._guest-onboarding-modal')
@endsection
