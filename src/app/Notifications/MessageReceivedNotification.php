<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MessageReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Message $message
    ) {
        //
    }

    /**
     * 通知の保存先
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * データベース通知に保存する内容
     */
    public function toArray(object $notifiable): array
    {
        $this->message->loadMissing(['sender.profile']);

        $senderName = $this->message->sender->profile->display_name
            ?? $this->message->sender->name;

        return [
            'type' => 'message',
            'title' => '新しいメッセージが届きました',
            'body' => $senderName . 'さんからメッセージが届きました',

            // 募集に紐づく messages.show ではなく、
            // ユーザー同士のDM画面へ遷移させる
            'url' => route('messages.users.show', $this->message->sender_id),

            'message_id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
        ];
    }
}
