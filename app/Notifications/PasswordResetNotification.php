<?php

namespace App\Notifications;

use App\Mail\BareMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    // プロパティ定義
    public $token;
    public $mail;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $token, BareMail $mail) // tokenと、インスタンス化したBareMailが引数
    {
        $this->token = $token;
        $this->mail = $mail;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return $this->mail
        ->from(config('mail.from.address'), config('mail.from.name'))
        ->to($notifiable->email) // $notifiableにはUserモデルが格納されている。つまり、対象ユーザのメールアドレスを取得している
        ->subject('[memo]パスワード再設定')
        ->text('emails.password_reset') // ここでメールのテンプレート（blade）を指定している
        ->with([ // withは、bladeに渡す変数
            'url' => route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->email,
            ]),
            'count' => config(
                'auth.passwords.' .
                config('auth.defaults.passwords') .
                '.expire'
            ),
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
