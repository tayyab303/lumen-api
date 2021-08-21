<?php

namespace App\Http\Middleware;

use Closure;
use App\Utils\HttpStatusCode;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
class Authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $userType
     * @return mixed
     */
    public function handle($request, Closure $next, $userType)
    {
        try{
            if (auth()->check()) {
                $user = explode(" ",$userType);
                if (in_array(auth()->user()->type, $user)) {
                    $index = array_search(auth()->user()->type, $user);
                    if(auth()->user()->type === intval($user[$index])){
                        return $next($request);
                    } else {
                        return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
                    }
                }else{
                    return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
                }
                
                // if(count($user)===1){
                //     if (auth()->user()->type === intval($user[0])) {
                //         return $next($request);
                //     }else{
                //         return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
                //     }
                // }else{
                //     if(($user[0] === '1' || $user[1]==='2' || $user[2] === '4')){
                //         if (auth()->user()->type === intval($user[0])) {
                //             return $next($request);
                //         }else if(auth()->user()->type === intval($user[1])){
                //             return $next($request);
                //         }else if(auth()->user()->type === intval($user[2])){
                //             return $next($request);
                //         }else{
                //             return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
                //         }
                //     }
                // }
            }
            else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::UNAUTHORIZED]], HttpStatusCode::UNAUTHORIZED);
            }
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
