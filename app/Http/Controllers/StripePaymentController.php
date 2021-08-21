<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Stripe;

class StripePaymentController extends Controller
{
    public function index()
    {
       return view('stripe');
    }

    public function process(Request $request)
    {
  		\Log::info($request->all());
        $stripe = Stripe::charges()->create([
            'source' => $request->get('tokenId'),
            'currency' => 'USD',
            'amount' => $request->get('amount') * 100
        ]);
  
        return $stripe;
    }
}