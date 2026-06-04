@extends('layouts.app')

@section('title', 'メッセージ')

@section('content')
<div
    data-message-polling
    data-latest-url="{{ route('messages.users.latest', $user) }}"
    data-latest-message-id="{{ $latestMessageId ?? 0 }}"
>
    @include('messages.user-show_sp')
    @include('messages.user-show_pc')
</div>

@endsection
