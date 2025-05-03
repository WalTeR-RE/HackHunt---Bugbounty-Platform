<?php

namespace App\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailService extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $emailType;

    /**
     * Create a new message instance.
     */
    public function __construct($data, $emailType)
    {
        $this->data = $data;
        $this->emailType = $emailType;
    }

    /**
     * Build the message.
     */
    public function build()
    {   
        switch ($this->emailType) {
            case 'reset':
                return $this->subject('Password Reset Request')
                            ->view('emails.reset_password')
                            ->with('data', $this->data);
            case 'invite':
                return $this->subject('You Have Been Invited!')
                            ->view('emails.invitation')
                            ->with('data', $this->data);
            case 'bounty':
                return $this->subject('Bounty Program Update')
                            ->view('emails.bounty_awarded')
                            ->with('data', $this->data);
            default:
                return $this->subject('Notification')
                            ->view('emails.generic')
                            ->with('data', $this->data);
        }
    }
}
