<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    /**
     * メール本文を作成する
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('【MokuMoku Match】メールアドレス認証のお願い')
            ->greeting('こんにちは。')
            ->line('MokuMoku Matchをご利用いただきありがとうございます。')
            ->line('募集の作成、応募、メッセージ送信などの機能を利用するには、メールアドレスの認証が必要です。')
            ->line('下のボタンをクリックして、メールアドレスの認証を完了してください。')
            ->action('メールアドレスを認証する', $verificationUrl)
            ->line('このメールに心当たりがない場合は、何も操作する必要はありません。')
            ->salutation('MokuMoku Match');
    }
}
