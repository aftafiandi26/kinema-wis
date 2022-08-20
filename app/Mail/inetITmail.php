<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class inetITmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to('anggarda.tiratana@frameworks-studios.com')
        ->cc('adinda.putri@frameworks-studios.com')
        ->cc('adisaputra.widjaja@frameworks-studios.com')
        ->cc('support@frameworks-studios.com')
        ->subject('Reminder - PR Internet')
        ->view('email.Notifikasi.IT.inetRemind');
    }
}
