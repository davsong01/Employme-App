<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Email extends Mailable
{
    use Queueable, SerializesModels;
    public $data, $name, $subject;

    public function __construct($data, $name, $subject)
    {
        $this->data = $data;
        $this->name = $name;
        $this->subject = $subject;
    }

    public function build()
    {
        
        return $this->markdown('emails.email')
        ->subject($this->subject);
    }
  
}
