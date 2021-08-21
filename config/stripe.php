<?php

return[

    'providers' => [

 Cartalyst\Stripe\Laravel\StripeServiceProvider::class 
],
 
'aliases' => [

 'Stripe' => Cartalyst\Stripe\Laravel\Facades\Stripe::class 
]
];