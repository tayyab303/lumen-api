<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Property;
use App\Models\User;
use App\Utils\AppConst;
use App\Utils\UserType;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class ReservationController extends Controller
{
    /**
     * private variable
     * 
     *  @var App\Models\Reservation $_comment  
     */
    private $_reserve;
    /**
     * array variable for validation
     * 
     */
    private $_validationRules=[
        'name' => 'required|string|regex:/^[\pL\s\-]+$/u|min:3|max:20',
        'phone_number' => 'required|min:9|max:15|regex:/[0-9]{9}/',   // allow only phone
        'email' => 'required|email',     // allow only email
        'message' => 'min:20|max:75',     // allow only email
    ];
    private $_customMessages = [
        'name.required' => 'The name field is required.',
        'name.max' => 'The name may not be greater than 3 characters.',
        'name.min' => 'The name must be at least 20 characters.',
        'name.regex' => 'The name format is invalid',

        'phone_number.required' => 'The phone number field is required.',
        'phone_number.max' => 'The phone number may not be greater than 15 characters.',
        'phone_number.min' => 'The phone number must be at least 9 characters.',
        'phone_number.regex' => 'The phone number format is invalid',

        'email.required' => 'The email field is required.',

        'message.max' => 'The message may not be greater than 75 characters.',
        'message.min' => 'The message must be at least 20 characters.',

    ];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Reservation $reserve)
    {
        $this->_reserve = $reserve;
    }
    
    /**
     * index function to show list.
     *
     * @return json response
     */
    public function index()
    {
      //
    }

    /**
     *  validate and store reservation
     * 
     * @param Request $req
     * @return json response
     */
    public function store(Request $request, $id)
    {
        $this->validate($request, $this->_validationRules, $this->_customMessages);
    try {
            $reserve = $this->_reserve->createReservation($request, $id);
    return response()->json(['entity'=> $reserve, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::CREATED]], HttpStatusCode::CREATED);
        }catch(\Exception $e) {
            Log::error('ReservationController -> store: ',$e);
        }
    }

}
