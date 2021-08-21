<?php

namespace App\Providers;

use App\Events\ExampleEvent;
use App\Listeners\ExampleListener;
use App\Events\AssignPropertyEvent;
use App\Listeners\AssignPropertyListener;
use App\Events\CreateCompanyEvent;
use App\Listeners\CreateCompanyListener;
use App\Events\CreateEmployeeEvent;
use App\Listeners\CreateEmployeeListener;
use App\Events\CreateCustomerEvent;
use App\Listeners\CreateCustomerListener;
use App\Events\PropertyStatusEvent;
use App\Listeners\PropertyStatusListener;
use App\Events\CreateCustomerForgotPasswordEvent;
use App\Listeners\CreateCustomerForgotPasswordListener;
use App\Events\CreateMemberForgotPasswordEvent;
use App\Listeners\CreateMemberForgotPasswordListener;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ExampleEvent::class => [
            ExampleListener::class,
        ],
        AssignPropertyEvent::class => [
            AssignPropertyListener::class,
        ],
        CreateCompanyEvent::class => [
            CreateCompanyListener::class,
        ],
        CreateEmployeeEvent::class => [
            CreateEmployeeListener::class,
        ],
        CreateCustomerEvent::class => [
            CreateCustomerListener::class,
        ],
        PropertyStatusEvent::class => [
            PropertyStatusListener::class,
        ],
        CreateCustomerForgotPasswordEvent::class => [
            CreateCustomerForgotPasswordListener::class,
        ],
        CreateMemberForgotPasswordEvent::class => [
            CreateMemberForgotPasswordListener::class,
        ],
    ];
}
