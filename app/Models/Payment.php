<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Property;



class Payment extends Model
{

    protected $fillable = [
        'user_id', 'stripe_payment_id', 'property_id', 'amount'
    ];

    /**
     * Relationship With User
     *
     * @return void
     */
     public function customer()
    {
        return $this->hasOne(Customer::class);
    }
     /**
     * Relationship With Property
     *
     * @return void
     */
     public function property()
    {
        return $this->hasOne(Property::class);
    }



     /**
     *
     * user stripe payment
     */
    public function makeStripePayment($data)
    {
        $paymentData['stripe_payment_id'] = $data['stripe_payment_id'];
        $paymentData['user_id'] = $data['user_id'];
        $paymentData['property_id'] = $data['property_id'];
        $paymentData['amount'] = $data['amount'];

        // dd($paymentData);
        return Payment::create($paymentData);
    }

    
}

