<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Utils\AppConst;
use App\Utils\UserType;
use App\Utils\HttpStatusCode;
use Firebase\JWT\JWT;
use App\Http\Resources\NotificationsResource;

class AuthController extends Controller
{
    /** @var App\Models\User $_user */
    private $_user;

    /**
     * Create a new token.
     *
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(User $user)
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'type' => $user->type, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60 * 60 // Expiration time
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    protected function jwtRefresh($user)
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'type' => $user->type, // Subject of the token
            'username' => $user->username, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + AppConst::ONE_MONTH // Expiration time one month in seconds 
        ];
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct(User $user, JWTGuard $jwt)
    public function __construct(User $user)
    {
        $this->_user = $user;
    }

    /**
     * register function
     *
     * @return void
     */
    public function register(Request $request)
    {

        //validate incoming request
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);


        try {
            $userData = $request->all();
            $user = User::create($userData);

            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED'], HttpStatusCode::CREATED);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], HttpStatusCode::CONFLICT);
        }
    }

    /**
     * login function
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        try {
            $email = $request->get('email');
            $password = $request->get('password');
            // Get user with credentials
            $user = User::where('email', $email)->where('password', hash(AppConst::HASH_ALGO, $password))->first();
            if ($user) {
                $check = User::where('email', $email)->where('is_verified', AppConst::VERIFIED)->first();
                if ($check) {
                    $token = $this->jwt($user);
                    $refreshToken = $this->jwtRefresh($user);
                    return response()->json([
                        'message' => 'User authenticated successfully!',
                        'type' => 'bearer',
                        'user' => $user,
                        'access_token' => $token,
                        'refresh_token' => $refreshToken,
                    ], HttpStatusCode::OK);
                } else {
                    return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_VERIFIED]], HttpStatusCode::NOT_VERIFIED);
                }
            } else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function renewToken(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'refresh_token' => 'required',
        ]);
        $username = $request->get('username');
        $refresh_token = $request->get('refresh_token');
        try {
            // Get user with credentials
            $decoded = JWT::decode($refresh_token, env('JWT_SECRET'), array(AppConst::JWT_ALGO));
            $user = User::where('username', $username)->first();
            if ($decoded->type === $user->type && $decoded->sub === $user->id && time() > $decoded->iat && time() < $decoded->exp) {
                $token = $this->jwt($user);
                return response()->json([
                    'message' => 'token authenticated successfully!',
                    'type' => 'bearer',
                    'access_token' => $token,
                ], HttpStatusCode::OK);
            } else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::UNAUTHORIZED]], HttpStatusCode::UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            dd($e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * get profile data function
     *
     * @return void
     */
    public function profile()
    {
        try {

            $profileData = Auth::user();

            if (Auth::user()->type == UserType::COMPANY) {
                Auth::user()->company;
            }
            if (Auth::user()->type == UserType::SUPER_EMPLOYEE || Auth::user()->type == UserType::COMPANY_EMPLOYEE) {
                Auth::user()->employee;
                Auth::user()->employee->company;
            }
            if (Auth::user()->type == UserType::CUSTOMER) {
                Auth::user()->customer;
            }

            // return new ProfileResource($profileData);
            return response()->json(['data' => $profileData,  'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * update profile data function
     *
     * @return void
     */
    public function updateProfile(Request $request)
    {
        try {

            $id = Auth::user()->id;

            $this->_user->updateProfile($request, $id);

            $profileData = $request->all();

            return response()->json(['data' => $profileData,  'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        } catch (\Throwable $th) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * get user notifications
     * @return mixed
     */
    public function notification()
    {
        try {
            if (auth::user()->type === UserType::SUPER_ADMIN) {
                $notification = auth::user()->notifications;
                return new NotificationsResource($notification);
            } elseif (auth::user()->type === UserType::COMPANY) {
                $notification = auth::user()->company->notifications;
                return new NotificationsResource($notification);
            } else {
                $notification = auth::user()->employee->notifications;
                return new NotificationsResource($notification);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }




    /**
     * member forgot password function
     *
     * @param Request $request
     * @return void
     */
    public function memberForgotPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);

        try {
            $email = $request->get('email');
            $user = User::where('email','=', $email)->first();
            if ($user) {
               $this->_user->resetMemberPasswordMail($email);
                return response()->json(['data' => "email sent",  'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
            } else if($user =  User::where('email','!=', $email)->first()){
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
            else{
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);

            }
        } catch (\Exception $e) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * member forgot password function
     *
     * @param Request $request
     * @return void
     */
    public function restoreMemberPassword(Request $request)
    {
        try {
            $token = $request->get('token');

            // Get user with credentials
            $user = User::where('token', $token)->first();
            if ($user) {

                return view('member.update-password', ['token' => $token]);
                // return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FOUND]], HttpStatusCode::FOUND);
            } else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::UNAUTHORIZED]], HttpStatusCode::UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * update password function
     *
     * @param Request $request
     * @return void
     */
    public function updateMemberPassword(Request $request)
    {
        try {
            $token = $request->get('token');
            $newpassword = $request->get('newpassword');
            $cpassword = $request->get('cpassword');
            // dd($token,$newpassword,$cpassword);
            if ($newpassword === $cpassword) {

                $this->_user->updateMemberPasswordSubmit($request->all());
                return redirect('http://localhost:3000/#/auth/login-page');
                // return response()->json(['data' => $res,  'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * update profile data function
     *
     * @return void
     */
    // public function updateLogo(Request $request)
    // {    
    //     try {

    //         if(Auth::user()->type === UserType::COMPANY){
    //             $id = Auth::user()->company_id;
    //         }

    //         $this->_user->updateLogo($request,$id);

    //         $profileData = $request->all();

    //         return response()->json(['data' => $profileData,  'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
    //     } catch (\Throwable $th) {
    //         return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
    //     }
    // }


      
}

