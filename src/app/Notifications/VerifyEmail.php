<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Hash;


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
        $hash = Hash::make($notifiable->email);

        return (new MailMessage)
            ->subject('メールアドレスの確認')
            ->line('メールアドレスを確認してください。')
            ->action('メールアドレスを確認', url('/email/verify', $notifiable->id . '?hash=' . $hash))
            ->line('このメールを無視しても問題ありません。');
    }

    public function build()
    {
        return $this->view('emails.verify')
                    ->subject('Email Verification');
    }
}
