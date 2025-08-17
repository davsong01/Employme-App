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

            if ($this->data['type'] == 'initial') {
                return $this->markdown('emails.bulk_email')
                    ->attachData($this->pdf->output(), "E-receipt.pdf")
                    ->subject('E - Receipt');
            }

            if ($this->data['type'] == 'pop') {
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
        
        return $this->markdown('emails.welcomemail')
            ->subject('E - Receipt');
    }
}
