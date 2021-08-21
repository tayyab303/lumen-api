<?php

namespace App\Providers;

use App\Models\User;
use Firebase\JWT\JWT;
use App\Utils\AppConst;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Firebase\JWT\SignatureInvalidException;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        Gate::policy(Customer::class,CustomerPolicy::class);
        Gate::policy(Company::class,CompanyPolicy::class);
        Gate::policy(Property::class,PropertyPolicy::class);
        Gate::policy(Employee::class,EmployeePolicy::class);
        // Gate::policy(Employee::class,EmployeePolicy::class);
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.
        $this->app['auth']->viaRequest('api', function ($request) {
            try{
                $authorization = $request->header('Authorization');
                if(isset($authorization) && $authorization !== null){
                    $authorization = str_replace("Bearer ", "", $authorization);
                    $decodedToken = JWT::decode($authorization, env('JWT_SECRET'), [AppConst::JWT_ALGO]);
                    if ($decodedToken && $decodedToken->iat + (60*60) === $decodedToken->exp) {
                        return User::find($decodedToken->sub);
                    }
                }
            }
            catch(SignatureInvalidException $e){
                throw $e;
            }
            catch(ExpiredException $e){
                throw $e;
            }
            catch(\Exception $e){
                throw $e;
            }
        });
    }

}
