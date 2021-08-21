<?php

namespace App\Listeners;

use App\Models\Employee;
use App\Events\ExampleEvent;
use App\Mail\AssignedProperty;
use App\Events\AssignPropertyEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignPropertyListener
{
    private $_mail;
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
     * @param  \App\Events\AssignPropertyEvent  $event
     * @return void
     */
    public function handle(AssignPropertyEvent $event)
    {
        Mail::to($event->employee->user->email)->send(new AssignedProperty($event->property));
    }
}
