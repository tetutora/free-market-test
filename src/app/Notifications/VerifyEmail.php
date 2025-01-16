<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends BaseVerifyEmail
{
    /**
     * メール認証リンクをカスタマイズして表示
     *
     * @param  \Illuminate\Contracts\Auth\MustVerifyEmail  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('メールアドレスの確認')
            ->line('メールアドレスを確認してください。')
            ->action('メールアドレスを確認', url('/email/verify', $notifiable->id . '?hash=' . sha1($notifiable->email)))
            ->line('このメールを無視しても問題ありません。');
    }
}
