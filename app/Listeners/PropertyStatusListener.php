<?php

namespace App\Listeners;

use App\Models\Property;
use App\Events\ExampleEvent;
use App\Mail\PropertyStatus;
use App\Events\PropertyStatusEvent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PropertyStatusListener
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
     * @param  \App\Events\PropertyStatusEvent  $event
     * @return void
     */
    public function handle(PropertyStatusEvent $event)
    {
        Mail::to($event->_property->company->user->email)->send(new PropertyStatus($event));
    }
}
