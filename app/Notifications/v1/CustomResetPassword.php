<?php

namespace App\Notifications\v1;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class CustomResetPassword extends ResetPasswordNotification
{
    use Queueable;

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Olá! Tudo bem?')
            ->subject('Redefinição de Senha - ' . config('app.name'))
            ->line('Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.')
            ->action('Redefinir Senha', $this->resetUrl($notifiable))
            ->line('Se você não solicitou a redefinição de senha, nenhuma ação adicional é necessária.');
    }

    protected function resetUrl($notifiable)
    {
        // Exemplo de uso para url front-end.
        // return url(
        //             env('APP_FRONTEND_URL') . 
        //             "/recover-password") . 
        //             '?' . 
        //             http_build_query([
        //             'token' => $this->token,
        //             'email' => $notifiable->getEmailForPasswordReset(),
        //             ]);

        return route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]); 
    }

}
