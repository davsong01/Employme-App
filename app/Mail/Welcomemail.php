<?php

namespace App\Mail;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Welcomemail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    public $pdf;
   
    public function __construct($data, $pdf)
    {
        $this->data = $data;
        $this->pdf = $pdf;

    }

    public function build()
    {
        if(isset($this->data['type']) && $this->data['type'] == 'notify_facilitator'){
            return $this->markdown('emails.notificationemail')
            ->subject('Notification to facilitate');
        }
        
        if(isset($this->data['type']) && $this->data['type'] == 'balance'){
            return $this->markdown('emails.welcomemail')
            ->subject('Balance Payment Received');
            
        }else{
            if($this->pdf && Str::contains($this->data['booking_form'], 'bookingforms')){
                    return $this->markdown('emails.welcomemail')
                        ->attachData($this->pdf->output(), "E-receipt.pdf")
                        ->subject('E - Receipt')
                        ->attach($this->data['booking_form'], [
                        'as' => 'Booking form.pdf',
                        'mime' => 'application/pdf',
                    ]);
            }else{
            
                return $this->markdown('emails.welcomemail')
                ->attachData($this->pdf->output(), "E-receipt.pdf")
                ->subject('E - Receipt');
            }
            
        }
        
    }
  
}
