<?php

namespace App\Listeners;

use App\Models\Company;
use App\Events\ExampleEvent;
use App\Mail\CompanyMail;
use App\Events\CreateCompanyEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateCompanyListener
{
    // private $_mail;
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
     * @param  \App\Events\CreateCompanyEvent  $event
     * @return void
     */
    public function handle(CreateCompanyEvent $event)
    {
      // dd($event);
      Mail::to($event->_company->user->email)->send(new CompanyMail($event));
    }
}
