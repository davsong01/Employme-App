<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Welcomemail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $details; 

    public $pdf;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $details, $pdf)
    {
        $this->data = $data;
        $this->details = $details;
        $this->pdf = $pdf;

    }

    public function build()
    {
        if(isset($this->data['type']) && $this->data['type'] == 'balance'){
            return $this->markdown('emails.welcomemail')
            ->subject('Balance Payment Received');
            
        }else{
            return $this->markdown('emails.welcomemail')
            ->attachData($this->pdf->output(), "E-receipt.pdf")
            ->subject('E - Receipt and Booking Form')
            ->attach($this->details['booking_form'], [
            'as' => 'Booking form.pdf',
            'mime' => 'application/pdf',
        ]);
        }
        

       // ->attachData($pdf->output(), "invoice.pdf");
    }
  
}
