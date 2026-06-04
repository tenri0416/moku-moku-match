@extends('layouts.app')

@section('title', 'メッセージ一覧')

@section('content')
<div
    data-message-index-polling
    data-latest-url="{{ route('messages.index.latest') }}"
>
    @include('messages.index_sp')
    @include('messages.index_pc')
</div>
@endsection
