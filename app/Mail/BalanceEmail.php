<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BalanceEmail extends Mailable
{
    use Queueable, SerializesModels;
   
    public $details; 
    public $pdf; 
    public $data;
    public function __construct($details, $data, $pdf)
    {

        $this->details = $details;

    }

    public function build()
    {
        
        return $this->markdown('emails.balance')
        ->attachData($this->pdf->output(), "E-receipt.pdf")
        ->subject('Balance payment received');
    }
  
}
