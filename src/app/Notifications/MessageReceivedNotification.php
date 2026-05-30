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
    }

    /**
     * 通知の保存先を指定する
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * DB通知として保存する内容
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'message',
            'title' => '新しいメッセージが届きました',
            'body' => $this->message->sender->name . 'さんからメッセージが届きました',
            'url' => route('messages.show', $this->message->workPost),
            'message_id' => $this->message->id,
            'work_post_id' => $this->message->work_post_id,
            'sender_id' => $this->message->sender_id,
        ];
    }
}
