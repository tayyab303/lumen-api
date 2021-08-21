<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Property;
use App\Models\User;
use App\Utils\AppConst;
use App\Utils\UserType;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class lnstallmentController extends Controller
{
    /**
     * private variable
     * 
     *  @var App\Models\Installment $_installment  
     */
    private $_installment;
    /**
     * array variable for validation
     * 
     */
    private $_validationRules=[
        'name' => 'required|string|regex:/^[\pL\s\-]+$/u|min:3|max:30',
        'ph' => 'required|min:9|max:15|regex:/[0-9]{9}/',   // allow only phone
        'details' => 'min:20|max:75',     // allow only email
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


        'details.max' => 'The message may not be greater than 75 characters.',
        'details.min' => 'The message must be at least 20 characters.',

    ];
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Installment $installment)
    {
        $this->_installment = $installment;
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
        $installment = $this->_installment->createLog($request, $id);
        try {
    return response()->json(['entity'=> $installment, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::CREATED]], HttpStatusCode::CREATED);
        }catch(\Exception $e) {
            Log::error('lnstallmentController -> store: ',$e);
        }
    }

}
