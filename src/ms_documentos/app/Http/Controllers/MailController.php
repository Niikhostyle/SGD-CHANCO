<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Documento;
use App\Models\DocumentoBuzon;
use App\Models\User;
use App\Models\Buzon;

class MailController extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $subject = "";
    public $user;
    public $documento;   
    public $documentoBuzon; 
    public $buzon;

    public function __construct(User $user, Documento $documento, DocumentoBuzon $documentoBuzon, Buzon $buzon)
    {
        $this->user = $user;
        $this->documento = $documento;
        $this->documentoBuzon = $documentoBuzon;
        $this->buzon = $buzon;
        $this->subject = "SGD - Nuevo documento ".' '." - ".' '. $documento->materia ;
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
    }
}
