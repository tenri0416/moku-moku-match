<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminUserRegisteredNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly User $user
    ) {
    }

    /**
     * 管理者通知はDBに保存する
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * DB通知に保存する内容
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'user_registered',
            'title' => 'ユーザーが新規登録されました。',
            'body' => 'ユーザーIDは' . $this->user->id . 'です。',
            'user_id' => $this->user->id,
        ];
    }
}
