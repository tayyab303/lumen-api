<?php

namespace App\Listeners;

use App\Models\Employee;
use App\Events\ExampleEvent;
use App\Mail\EmployeeMail;
use App\Events\CreateEmployeeEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateEmployeeListener
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
     * @param  \App\Events\CreateEmployeeEvent  $event
     * @return void
     */
    public function handle(CreateEmployeeEvent $event)
    {
      Mail::to($event->_employee->user->email)->send(new EmployeeMail($event));
    }
}
