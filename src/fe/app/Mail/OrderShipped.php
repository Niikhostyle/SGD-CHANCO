<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Documento;
use App\Models\User;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * 
     * @var \App\Models\Documento
     * 
     */

    public $user;   

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       
        return $this->markdown('mails.emails')
            ->with(['name' => $this->user->email]);

        return $this->from('example@example.com')
        ->markdown('mails.emails');
    }
}
