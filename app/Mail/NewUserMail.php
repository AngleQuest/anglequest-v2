<?php

namespace App\Mail;

use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\WebsiteSetting;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewUserMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user
    ) {
    }

    /**
     * Get the message envelope.
     */


    public function build()
    {
        $email_content = EmailTemplate::find(4);
        $site_details = WebsiteSetting::first();
        $searches = array('{YEAR}', '{COMPANY_EMAIL}');
        $replacements = array(date('Y'), $site_details->business_email);
        return $this->subject('Welcome mail from '. $site_details->business_name)
            ->view('emails.new-user-mail', [
                'logo' => $site_details->logo_url,
                'email_content' => str_replace('{USERNAME}', ucfirst($this->user['username']), $email_content->content),
                'footer' =>str_replace($searches,$replacements,$email_content->footer)
            ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
