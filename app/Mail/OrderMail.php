<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->orderItemId = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return 
         $this->subject('Megrendezés érkezett')
         	  ->view('cart.orderemail',
        	            ['orderItemId' => $this->orderItemId]);
    }
}
