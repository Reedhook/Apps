<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Queue\Factory as Queue;

class CustomResetPassword extends ResetPassword implements Mailable
{


    public function send($mailer)
    {
        // TODO: Implement send() method.
    }

    public function queue(Queue $queue)
    {
        // TODO: Implement queue() method.
    }

    public function later($delay, Queue $queue)
    {
        // TODO: Implement later() method.
    }

    public function cc($address, $name = null)
    {
        // TODO: Implement cc() method.
    }

    public function bcc($address, $name = null)
    {
        // TODO: Implement bcc() method.
    }

    public function to($address, $name = null)
    {
        // TODO: Implement to() method.
    }

    public function mailer($mailer)
    {
        // TODO: Implement mailer() method.
    }
}
