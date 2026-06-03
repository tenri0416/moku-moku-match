@extends('layouts.app')

@section('title', 'メッセージ一覧')

@section('content')

@include('messages.index_sp')
@include('messages.index_pc')
@endsection
