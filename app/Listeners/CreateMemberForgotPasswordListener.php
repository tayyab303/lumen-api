<?php

namespace App\Listeners;

use App\Events\CreateMemberForgotPasswordEvent;
use App\Mail\MemberForgotPasswordMail;
use Illuminate\Support\Facades\Mail;

class CreateMemberForgotPasswordListener
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
     * @param  CreateMemberForgotPasswordEvent  $event
     * @return void
     */
    public function handle(CreateMemberForgotPasswordEvent $event)
    {
        // dd($event->_user);
        Mail::to($event->_user->email)->send(new MemberForgotPasswordMail($event->_user));

    }
}
