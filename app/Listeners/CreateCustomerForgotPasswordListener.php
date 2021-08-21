<?php

namespace App\Listeners;

use App\Events\CreateCustomerForgotPasswordEvent;
use App\Mail\CustomerForgotPasswordMail;
use Illuminate\Support\Facades\Mail;

class CreateCustomerForgotPasswordListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Mail $mail)
    {
        $this->_mail = $mail;
    }

    /**
     * Handle the event.
     *
     * @param  CreateCustomerForgotPasswordEvent  $event
     * @return void
     */
    public function handle(CreateCustomerForgotPasswordEvent $event)
    {
        Mail::to($event->_customer->user->email)->send(new CustomerForgotPasswordMail($event->_customer));

    }
}
