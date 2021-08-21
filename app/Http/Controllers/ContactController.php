<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    private $_validationRules = [
        'name' => 'required|string|regex:/^[\pL\s\-]+$/u|min:3|max:30',
        'ph' => 'required|min:9|max:15',   // allow only phone
        'email' => 'required|email',     // allow only email
        'description' => 'required|min:20|max:200',     // allow only email
    ];

    private $_customMessages = [
        'name.required' => 'The name field is required.',
        'name.max' => 'The name may not be greater than 3 characters.',
        'name.min' => 'The name must be at least 30 characters.',
        'name.regex' => 'The name format is invalid',

        'ph.required' => 'The phone number field is required.',
        'ph.max' => 'The phone number may not be greater than 15 characters.',
        'ph.min' => 'The phone number must be at least 9 characters.',
        'ph.regex' => 'The phone number format is invalid',

        'email.required' => 'The email field is required.',

        'description.max' => 'The message may not be greater than 200 characters.',
        'description.min' => 'The message must be at least 20 characters.',

    ];
    /**
     * Create a new controller instance.
     * @var App\Models\Contact $_contact
     * @return void
     */
    public function __construct(Contact $contact)
    {
        $this->_contact = $contact;
    }
    
    /**
     * @param Request $request
     */
    public function contactUs(Request $request)
    {
        $this->validate($request, $this->_validationRules, $this->_customMessages);
        try {
            $result = $this->_contact->contactUs($request->all());
            return response()->json(['entity'=> $result, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::CREATED]], HttpStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('CustomerController -> index: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

}
