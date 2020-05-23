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
    // public $programName; 
    // public $programAbbr; 
    // public $message;
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
        
        return $this->markdown('emails.welcomemail')
        ->attachData($this->pdf->output(), "E-receipt.pdf")
        ->subject('E - Receipt and Booking Form')
        ->attach($this->details['booking_form'], [
            'as' => 'Booking form.pdf',
            'mime' => 'application/pdf',
        ]);

       // ->attachData($pdf->output(), "invoice.pdf");
    }
  
}
