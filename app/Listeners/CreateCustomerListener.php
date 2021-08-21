<?php

namespace App\Listeners;

use App\Models\Customer;
use App\Events\ExampleEvent;
use App\Mail\CustomerMail;
use App\Events\CreateCustomerEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateCustomerListener
{
    // private $_mail;
    /**ac
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
     * @param  \App\Events\CreateCustomereEvent  $event
     * @return void
     */
    public function handle(CreateCustomerEvent $event)
    {
      Mail::to($event->_customer->user->email)->send(new CustomerMail($event->_customer));
    }
}
