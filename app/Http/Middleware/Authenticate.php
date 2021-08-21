<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Utils\HttpStatusCode;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try{
            if ($this->auth->guard($guard)->guest()) {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::UNAUTHORIZED]], HttpStatusCode::UNAUTHORIZED);
            }

            return $next($request);
        }
        catch(SignatureInvalidException $e){
            return response()->json(['message' => $e->getMessage()], HttpStatusCode::UNAUTHORIZED);
        }
        catch(ExpiredException $e){
            return response()->json(['message' => $e->getMessage()], HttpStatusCode::UNAUTHORIZED);
        }
        catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], HttpStatusCode::UNAUTHORIZED);
        }
    }
}
