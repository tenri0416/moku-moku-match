@extends('layouts.app')

@section('title', 'メッセージ一覧')

@section('content')
@php
    $loginUserId = auth()->id();

    /**
     * メッセージ一覧用の表示データを組み立てる
     */
    $messageItems = collect($messages ?? [])
        ->map(function ($message) use ($loginUserId) {
            $partner = (int) $message->sender_id === (int) $loginUserId
                ? $message->receiver
                : $message->sender;

            if (! $partner) {
                return null;
            }

            $profile = $partner?->profile;

            $avatarPath = $profile?->avatar_path;
            $avatarUrl = $avatarPath
                ? asset('storage/' . ltrim($avatarPath, '/'))
                : asset('images/default-avatar.png');

            $displayName = $profile?->display_name
                ?? $partner?->name
                ?? 'ユーザー';

            $jobType = $profile?->job_type
                ?? '職種未設定';

            $unreadCount = \App\Models\Message::query()
                ->where('sender_id', $partner->id)
                ->where('receiver_id', $loginUserId)
                ->whereNull('read_at')
                ->count();

            $isMine = (int) $message->sender_id === (int) $loginUserId;

            return [
                'message' => $message,
                'partner' => $partner,
                'profile' => $profile,
                'display_name' => $displayName,
                'job_type' => $jobType,
                'avatar_url' => $avatarUrl,
                'unread_count' => $unreadCount,
                'is_mine' => $isMine,
                'last_body' => \Illuminate\Support\Str::limit($message->body, 80),
                'pc_time' => optional($message->created_at)->format('Y/m/d H:i'),
                'sp_time' => optional($message->created_at)->isToday()
                    ? optional($message->created_at)->format('H:i')
                    : (optional($message->created_at)->isYesterday()
                        ? '昨日'
                        : optional($message->created_at)->format('n/j')),
            ];
        })
        ->filter()
        ->values();

    $conversationCount = $messageItems->count();
    $totalUnreadCount = $messageItems->sum('unread_count');
    $replyRate = 0;
@endphp

@include('messages.index_sp')
@include('messages.index_pc')
@endsection
