<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sender, $msg)
    {
        $this->sender = $sender;
        $this->msg = $msg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return 
         $this->subject('Megrendezés kezelés üzenet')
         	  ->view('order.confirmmail',
        	            ['sender' => $this->sender, "msg" => $this->msg]);
    }
}
