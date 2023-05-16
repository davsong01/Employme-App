<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class POPemail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
   
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;

    }

    public function build()
    {
        
        return $this->markdown('emails.popemail')
        ->subject('Proof of Payment')
        ->attach($this->data['pop'], [
            'as' => 'Proof of Payment',
        ]);
        
    }
  
}
