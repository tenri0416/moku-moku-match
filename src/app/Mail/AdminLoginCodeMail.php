<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminLoginCodeMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $code
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('【MokuMoku Match】管理者ログイン認証コード')
            ->view('emails.admin-login-code');
    }
}
