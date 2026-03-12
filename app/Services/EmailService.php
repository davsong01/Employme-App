<?php

namespace App\Services;

use App\Enums\EmailType;
use App\Mail\NotificationMail;
use App\Models\EmailProvider;
use App\Models\NonCriticalEmail;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Log and optionally send an email
     */
    public static function sendEmail(
        $type, 
        $sender,
        $preamble = null,
        $recipient_name, 
        $recipient_email, 
        $priority=1, 
        
        $provider_id=null, 
        $attachments=null, 
    ){
        $emailContent = self::emailContent($type);
        dd($emailContent);
        $subject = $emailContent['subject'];
        $content = $emailContent['content'];
        
        $data = [
            'status' => 0,
            'sender' => $sender,
            'type' => $type,
            'subject' => $subject,
            'preamble' => $preamble,
            'content' => $content,
            'priority' => $priority,
            'provider_id' => $provider_id ?? 1,
            'recipient_name' => $recipient_name,
            'recipient' => $recipient_email,
            'attachments' => !is_null($attachments) ? json_encode($attachments) : NULL,
        ];
        // log

        $non_critical = NonCriticalEmail::create($data);

        if($priority == 0){
            $res = self::sendEmailReal($data);

            if($res['message'] == 'success'){
                $non_critical->update([
                    'status' => 1,
                    'sent_at' => now(),
                ]);

                return [
                    'status' => true,
                    'log' => $non_critical
                ];
            }else{
                $non_critical->update([
                    'status' => 0,
                    'errors' => $res['errors'] ?? 'something went wrong',
                ]);
            }

            return [
                'status' => false,
                'log' => $non_critical

            ];
        }

        return [
            'status' => true,
            'log' => $non_critical

        ];
        // send

    }

    /**
     * Send the email using provider configuration
     */
    public static function sendEmailReal($data)
    {
        $data['provider_id'] = $data['provider_id'] ?? 1;
        $provider = EmailProvider::where('id',$data['provider_id'])->first();

        config([
            'mail.driver' => $provider->MAIL_DRIVER,
            'mail.host'=> $provider->MAIL_HOST,
            'mail.port' => $provider->MAIL_PORT,
            'mail.encryption' => $provider->MAIL_ENCRYPTION,
            'mail.username' => $provider->MAIL_USERNAME,
            'mail.password' => $provider->MAIL_PASSWORD,
            'mail.replyToName' => $provider->MAIL_REPLY_TO_NAME,
            'mail.replyToAddress' => $provider->MAIL_REPLY_TO_ADDRESS,
            'mail.from' => [
                'address' => $provider->MAIL_FROM_ADDRESS,
                'name' => $provider->MAIL_FROM_NAME,
            ],
        ]);
        $data['provider'] = $provider->toArray();

        // return new NotificationMail($data);

        try {
            Mail::to($data['recipient'])->send(new NotificationMail($data));
            return [
                'message' => 'success',
                'provider' => $data['provider'],
            ];
        } catch (\Exception $e) {
                $provider = EmailProvider::where('status','active')->where('id','!=',$data['provider_id'])->first();
                if(isset($provider) && !empty($provider)){
                    config([
                        'mail.driver' => $provider->MAIL_DRIVER,
                        'mail.host'=> $provider->MAIL_HOST,
                        'mail.port' => $provider->MAIL_PORT,
                        'mail.encryption' => $provider->MAIL_ENCRYPTION,
                        'mail.username' => $provider->MAIL_USERNAME,
                        'mail.password' => $provider->MAIL_PASSWORD,
                        'mail.replyToName' => $provider->MAIL_REPLY_TO_NAME,
                        'mail.replyToAddress' => $provider->MAIL_REPLY_TO_ADDRESS,
                        'mail.from' => [
                            'address' => $provider->MAIL_FROM_ADDRESS,
                            'name' => $provider->MAIL_FROM_NAME,
                        ],
                    ]);

                    $data['provider'] = $provider->toArray();
                    try {
                        Mail::to($data['recipient'])->send(new NotificationMail($data));
                    }catch(\Exception $e){
                        return [
                            'message' => 'error',
                            'error' => 'Retry 2:' . $e->getMessage(),
                        ];
                    }

                }

        }
    }

    /**
     * Build the email content based on type
     */
    public function emailContent(array $data): string
    {
        $content = '';

        switch (EmailType::from($data['type'])) {

            case 'deactivation':
                $content = 'Dear '.$data['name'].',<br><p style="text-align: justify"><br>This is to formally notify you that the 60 days post induction grace period to have your profile still domiciled on waacsp Certified members portal have since elapsed.Subsequently, your profile is now on <strong>sleep mode</strong> meaning your profile is no longer visible and can not be used by you nor seen by the public. <br> Your profile and records on WAACSP will be permanently deleted after 30 days on this mode and will require a fresh process for membership. <br> <br>
                To re-affirm and revert your profile to the status quo ante, kindly contact your waccsp affiliate partner or send us a mail at: waascp@gmail.com</p> <br><br><br>
                - ADMIN TEAM -';
            break;

            case 'Member':
                $content = 'Dear '.$data['first-name'].',<br><br>Thank you on your interest in this family of West African Association of Customer Service Professionals. <br><br>Please note that your application is being reviewed.<br>We will contact you on the status of your application as soon as the process is completed.<br>';
            break;

            case 'sendPaymentLink':
                $content = 'Dear '.$data['name'].',<br>After vetting and due evaluation, we are delighted to inform you that your application to join the West Africa Association of Customer Service professionals was approved by the association.<br>Subsequently, you status have been upgraded to <strong style="color:orange">PENDING</strong>. <br><br>Proceed to pay the membership fee of $70 (or equivalent in your local currency) using the payment link below.<br>';

                $content .= '<br><a href="'.$data['link'].'" style="background-color: #163d51; border-radius: 4px; color: #ffffff; display: inline-block;font-size: 16px; line-height: 40px; text-align: center; text-decoration:none; width: 180px;" target="_blank">Pay Now</a><br>';

                $content .= '<strong>Should you be unable to transact with the link above and or will prefer other payment options, kindly reply to this mail to that effect and stating your country of residence and alternative payment details will be provided for you.</strong> <br><br>Upon receipt of your payment baring any other circumstances, your membership will be integrated to APPROVED.';
            break;

            case 'Trainer':
                $content = 'Thank you for your interest in partnerring with the West African Association of Customer Service Professionals as an instructor. <br><br>Please note that your application is being considered.<br>We shall contact you as soon as possible.';
            break;

            case 'Partner':
                $content = 'Thank you for your interest in partnerring with the West African Association of Customer Service Professionals. <br>Please note that your application is being considered. <br>We shall contact you as soon as possible.';
            break;

            case 'Employer':
                $content = 'Dear '.$data['name'].',<br>Thank you for your interest in '.$data['fname'].', Please note that our representative will contact you soon.<br>';
                $content .= "<b>Membership ID:</b> ".$data['membership_id']."<br>";
                $content .= "<b>Full Name:</b> ".$data['fname']." ".$data['lname']." ".$data['mname'];
            break;

            case 'Approve':

                if ($data['application_type'] === 'Member') {

                    $content = 'Dear '.$data['name'].',<br><br>Your application for membership of The West Africa Association of Customer Service Professionals has been approved. <br>We are delighted to inform you that you are now a certified member of the WAACSP. <br>Your membership ID is: <b>'.$data['membership_id'].'</b><br>';

                    $content .= '<br><a href="'.config('app.url').'/english/profile/'.$data['id'].'" style="background-color:#163d51;color:#fff;display:inline-block;line-height:40px;text-align:center;text-decoration:none;width:300px;" target="_blank">View your live profile</a><br><br>';

                    $content .= 'Username: '.$data['membership_id'].'<br>Password: WAACSP12345<br>';

                    $content .= '<br><a href="'.config('app.url').'/account" style="background-color:#163d51;color:#fff;display:inline-block;line-height:40px;text-align:center;text-decoration:none;width:300px;" target="_blank">Login to your Portal here</a>';
                }

                if ($data['application_type'] === 'Professional') {

                    $content = 'Dear '.$data['name'].',<br>Your application for membership has been approved. You are now a professional member of WAACSP.<br>';
                    $content .= 'Membership ID: <b>'.$data['membership_id'].'</b><br>';

                    $content .= '<br><a href="'.config('app.url').'/english/profile/'.$data['id'].'" style="background-color:#163d51;color:#fff;display:inline-block;line-height:40px;text-align:center;text-decoration:none;width:300px;" target="_blank">View your live profile</a><br>';
                }

            break;

            case 'Renew':
                $content = 'Dear '.$data['name'].',<br>Your membership has been renewed for 2 years.<br>';
                $content .= '<br><a href="'.config('app.url').'/english/profile/'.$data['id'].'" target="_blank">View your live profile</a>';
            break;

            case 'Unapprove':
                $content = 'Dear '.$data['name'].',<br>We regret to inform you that your membership has been withdrawn.';
            break;

            case 'Featured':
                $content = 'Dear '.$data['name'].',<br>Your profile has been featured on the homepage of '.config('app.name').'.';
            break;

            case 'Email':
                $content = 'Dear '.$data['name'].'<br>'.$data['content'];
            break;

            case 'otp':
                $content = 'Dear '.$data['name'].'<br>Your OTP Code is: '.$data['otp'];
            break;

            case 'license_expired':
                $content = 'Dear '.$data['name'].',<br>Your membership tenure of 2 years on WAACSP has expired.';
            break;

            case 'trainerapply':
                $content = "Thank you on your interest to join the WAACSP Trainers Network (WTN).<br><br>
                PLEASE READ CAREFULLY:<br><br>
                Application fee is \$10 for members or \$15 for non members.<br><br>
                Please chat our WhatsApp line +233(0)556520112 to confirm payment category.";
            break;

            case 'trainerapply.notifyadmin':
                $content = 'Someone has registered for the WAACSP Trainers network:<br>
                Name: '.$data['t_name'].'<br>
                Phone: '.$data['phone'].'<br>
                Email: '.$data['email'];
            break;

            case 'payment':
                $content = 'Payment Received<br>
                Name: '.$data['t_name'].'<br>
                Phone: '.$data['phone'].'<br>
                Email: '.$data['email'];
            break;

            default:
                $content = '';
            break;
        }

        return $content;
    }
}