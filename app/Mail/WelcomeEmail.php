<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $beneficio;
    public $url_login;

    public function __construct($user)
    {
        $this->user = $user;
        $this->beneficio = 'otimizar seus processos';
        $this->url_login = route('login');
    }

    public function build()
    {
        return $this->markdown('emails.welcome')
                    ->subject('🎉 Bem-vindo(a) ao ' . config('app.name') . '!');
    }
}
