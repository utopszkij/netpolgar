<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $mailbody)
    {
        $this->subject = $subject;
        $this->mailbody = $mailbody;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return 
         $this->subject($this->subject)
         	  ->view('newslettermail',
        	            ['mailbody' => $this->mailbody]);
    }
}
