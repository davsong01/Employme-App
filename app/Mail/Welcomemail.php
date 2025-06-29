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
   
    public function __construct($data,$pdf)
    {
        $this->data = $data;
        $this->pdf = $pdf;

    }

    // public function build()
    // {
    //     // Check if 'type' is set in data and its value
    //     if (isset($this->data['type'])) {
    //         if ($this->data['type'] == 'notify_facilitator') {
    //             return $this->markdown('emails.notificationemail')
    //                 ->subject('Notification to facilitate');
    //         } elseif ($this->data['type'] == 'balance') {
    //             return $this->markdown('emails.welcomemail')
    //                 ->subject('Balance Payment Received');
    //         } else{
    //             return $this->markdown('emails.bulk_email')
    //             ->subject($this->data['subject'] ?? 'Welcome To '.config('app.name'));
    //         }
    //     }

    //     // Check if PDF exists and if 'booking_form' contains 'bookingforms'
    //     if ($this->pdf && Str::contains($this->data['booking_form'], 'bookingforms')) {
    //         return $this->markdown('emails.welcomemail')
    //             ->attachData($this->pdf->output(), "E-receipt.pdf")
    //             ->subject('E - Receipt')
    //             ->attach($this->data['booking_form'], [
    //                 'as' => 'Booking form.pdf',
    //                 'mime' => 'application/pdf',
    //             ]);
    //     }

    //     // If PDF exists without 'booking_form', just attach the PDF
    //     if ($this->pdf) {
    //         return $this->markdown('emails.welcomemail')
    //             ->attachData($this->pdf->output(), "E-receipt.pdf")
    //             ->subject('E - Receipt');
    //     }

    //     // Default case: return the email without any attachment
    //     return $this->markdown('emails.welcomemail')
    //         ->subject('E - Receipt');
    // }
    public function build()
    {
        $subject = $this->data['subject'] ?? 'Welcome To ' . config('app.name');

        if (isset($this->data['type'])) {
            if ($this->data['type'] === 'notify_facilitator') {
                return $this->markdown('emails.notificationemail')
                    ->subject('Notification to facilitate');
            }

            if ($this->data['type'] === 'balance') {
                return $this->markdown('emails.welcomemail')
                    ->subject('Balance Payment Received');
            }

            if ($this->data['type'] === 'bulk') {
                $email = $this->markdown('emails.bulk_email')
                    ->subject($subject);

                if (!empty($this->data['attachments']) && is_array($this->data['attachments'])) {
                    foreach ($this->data['attachments'] as $attachmentPath) {
                        if (file_exists($attachmentPath)) {
                            $email->attach($attachmentPath, [
                                'as' => basename($attachmentPath),
                                'mime' => mime_content_type($attachmentPath),
                            ]);
                        }
                    }
                }

                return $email;
            }
        }

        // Fallback logic for receipt with optional booking form
        if ($this->pdf && isset($this->data['booking_form']) && Str::contains($this->data['booking_form'], 'bookingforms')) {
            return $this->markdown('emails.welcomemail')
                ->attachData($this->pdf->output(), "E-receipt.pdf")
                ->attach($this->data['booking_form'], [
                    'as' => 'Booking form.pdf',
                    'mime' => 'application/pdf',
                ])
                ->subject('E - Receipt');
        }

        if ($this->pdf) {
            return $this->markdown('emails.welcomemail')
                ->attachData($this->pdf->output(), "E-receipt.pdf")
                ->subject('E - Receipt');
        }

        return $this->markdown('emails.welcomemail')
            ->subject('E - Receipt');
    }
}
