<?php

use App\Models\Mailing;


if (! function_exists('mailSend')) {
    function mailSend($type, $recipient, $subject, $mail_class, $payloadData = [])
    {
        $data = [
            'type' => $type,
            'email' => $recipient->email,
            'subject' => $subject,
            'body' => "",
            'mailable' => $mail_class,
            'scheduled_at' => now(),
            'payload' => array_merge($payloadData)
        ];

        Mailing::saveData($data);
    }
}
