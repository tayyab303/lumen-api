<?php

use \App\Utils\UserType;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->group(['PublicRoutes', 'prefix' => 'api'], function () use ($router) {
    // API route group
    $router->group(['AuthController'], function () use ($router) {
        // Matches "/api/register
        $router->post('register', 'AuthController@register');

        // Matches "/api/login
        $router->post('login', 'AuthController@login');

         // renew-tokens
        $router->post('/renew-token', ['uses' => 'AuthController@renewToken', 'as' => 'renewToken']);

        // customer forgot password routes
        $router->post('/forgot-password', ['uses' => 'CustomerController@forgotPassword' , 'as' => 'forgotPassword']);
        $router->get('/reset-password', ['uses' => 'CustomerController@restorePassword', 'as' => 'restorePassword']);
        $router->post('/update-password', ['uses' => 'CustomerController@updatePassword', 'as' => 'updatePassword']);
        
        // admin,company,employee forgot password routes
        $router->post('/forgot-member-password', ['uses' => 'AuthController@memberForgotPassword' , 'as' => 'memberForgotPassword']);
        $router->get('/restore-member-password', ['uses' => 'AuthController@restoreMemberPassword' , 'as' => 'restoreMemberPassword']);
        $router->post('/update-member-password', ['uses' => 'AuthController@updateMemberPassword', 'as' => 'updateMemberPassword']);

        
    });
    $router->group(['CustomerController'], function () use ($router) {
        //Customer Registration
        $router->post('/customer-register', ['uses' => 'CustomerController@store', 'as' => 'customerStore']);
        $router->get('/customer-verify', ['uses' => 'CustomerController@verify', 'as' => 'customerVerify']);

    });

    //Property Controller
    $router->group(['PropertyController', 'prefix' => 'property'], function () use ($router){
        $router->get('/view/{id}', ['uses' => 'PropertyController@view', 'as' => 'propertyView']);
        $router->get('/search', ['uses' => 'PropertyController@search', 'as' => 'propertySearch']);
        $router->get('/featured', ['uses' => 'PropertyController@featured', 'as' => 'propertyFeatured']);
    });
    
    // Contact Controller
    $router->group(['ContactController', 'prefix' => 'contact'], function () use ($router){
        $router->post('/contact-us', ['uses' => 'ContactController@contactUs', 'as' => 'contactUs']);
    });

    // Statistic Controller
    $router->group(['StatisticController', 'prefix' => 'statistic'], function () use ($router){
        $router->get('/stats', ['uses' => 'StatisticController@stats', 'as' => 'stats']);
    });

    // Reservation Controller
    $router->group(['ReservationController', 'prefix' => 'reserve'], function () use ($router){
        $router->post('/store/{id}', ['uses' => 'ReservationController@store', 'as' => 'storeReservation']);
    });
    // InstallmentslOg Controller
    $router->group(['lnstallmentController', 'prefix' => 'installment'], function () use ($router){
        $router->post('/store/{id}', ['uses' => 'lnstallmentController@store', 'as' => 'storeLog']);
    });
    /**
     * CompanyController
     */
    $router->group(['CompanyController', 'prefix' => 'company'],function() use ($router) {
        $router->get('/search', ['uses' => 'CompanyController@search', 'as' => 'companySearch']);
    });

    // $router->group(['StripePaymentController@stripe','prefix' => 'payment'], function () use ($router) {
        //Customer Registration
        //$router->get('/checkout', ['uses' => 'CheckoutController@checkout', 'as' => 'checkout']);
        //$router->post('/stripe', ['uses' => 'StripePaymentController@process', 'as' => 'stripe']);
//         Route::get('checkout','CheckoutController@checkout');
// Route::post('checkout','CheckoutController@afterpayment')->name('checkout.credit-card');

     
    //});
    // $router->group(['CustomerController'],function() use ($router) {
    //       $router->post('/payment', ['uses' => 'CustomerController@paymentProcess', 'as' => 'paymentProcess']);
        
    // });
});


$router->group(['Private', 'prefix' => 'api', 'middleware' => 'auth'], function() use ($router){
    /**
     * All Users
     */
    $router->group(['AllUser'], function () use ($router) {
        $router->get('/', function () use ($router) {
            return response()->json(['version' => $router->app->version(), 'message' => 'Welcome to the Properietors']);
        });
        /**
         * CompanyController
         */
        $router->group(['CompanyController', 'prefix' => 'company'],function() use ($router) {
            $router->get('/show/{id}', ['uses' => 'CompanyController@show', 'as' => 'companyShow']);
        });
        /**
         * CategoryController
         */
        $router->group(['CategoryController', 'prefix' => 'category'], function () use ($router) {
            $router->get('/list', ['uses' => 'CategoryController@index', 'as' => 'categoryList']);
        });
        /**
         * PropertyController
         */
        $router->group(['PropertyController', 'prefix' => 'property'], function () use ($router) {
            $router->get('/list', ['uses' => 'PropertyController@index', 'as' => 'propertyList']);
            $router->get('/show/{id}', ['uses' => 'PropertyController@show', 'as' => 'propertyShow']);
        });

        
    });
    


   

    /**
     * Super Admin, Company and Employee Routes
     */
    $router->group(['SuperAdminCompany', 'middleware' => 'authorize:'.UserType::SUPER_ADMIN.' '.UserType::COMPANY.' '.UserType::SUPER_EMPLOYEE.' '.UserType::COMPANY_EMPLOYEE], function() use ($router){
        /**
         * EmployeeController
         */
        $router->group(['EmployeeController', 'prefix' => 'employee'], function () use ($router) {
            $router->get('/show/{id}', ['uses' => 'EmployeeController@show', 'as' => 'employeeShow']);
        });
        /**
         * Comment Controller
         */
        $router->group(['CommentController', 'prefix' => 'comment'], function () use ($router) {
            $router->post('/add-comment/{id}', ['uses' => 'CommentController@store', 'as' => 'addComment']);
            $router->get('/show/{id}', ['uses' => 'CommentController@show', 'as' => 'commentShow']);
            $router->get('/list/{id}', ['uses' => 'CommentController@index', 'as' => 'commentList']);
        });

        /**
         * AuthController profile data
         */
        $router->group(['AuthController'], function () use ($router) {
            $router->get('/profile', ['uses' => 'AuthController@profile' , 'as' => 'userProfile']);
            $router->get('/notification', ['uses' => 'AuthController@notification' , 'as' => 'getNotification']);
            $router->post('/update-profile', ['uses' => 'AuthController@updateProfile' , 'as' => 'updateProfile']);
        //    $router->post('/payment', ['uses' => 'AuthController@paymentProcess', 'as' => 'paymentProcess']);

        });

        /**
         * statistic controller
         */
    $router->group(['StatisticController', 'prefix' => 'statistic'], function () use ($router){
        $router->get('/admin-stats', ['uses' => 'StatisticController@adminStats', 'as' => 'adminStats']);
    });

    });
    
    /**
     * Super Admin and Company Routes
     */
    $router->group(['SuperAdminCompany', 'middleware' => 'authorize:'.UserType::SUPER_ADMIN.' '.UserType::COMPANY.' '.' '], function() use ($router){
        /**
         * CompanyController
         */
        $router->group(['CompanyController', 'prefix' => 'company'],function() use ($router) {
            $router->post('/logo/{id}', ['uses' => 'CompanyController@companyLogoImage', 'as' => 'companyLogo']);
            $router->get('/edit/{id}', ['uses' => 'CompanyController@edit', 'as' => 'companyEdit']);
            $router->put('/update/{id}', ['uses' => 'CompanyController@update', 'as' => 'companyUpdate']);
            $router->delete('/delete/{id}', ['uses' => 'CompanyController@destroy', 'as' => 'companyDelete']);
        });
        /**
         * CustomerController
         */
        $router->group(['CustomerController', 'prefix' => 'customer'],function() use ($router) {
            $router->get('/show/{id}', ['uses' => 'CustomerController@show', 'as' => 'customerShow']);
        });
        /**
         * PropertyController
         */
        $router->group(['PropertyController', 'prefix' => 'property'], function () use ($router) {
            $router->post('/store', ['uses' => 'PropertyController@store', 'as' => 'propertyStore']);
            $router->get('/edit/{id}', ['uses' => 'PropertyController@edit', 'as' => 'propertyEdit']);
            $router->put('/update/{id}', ['uses' => 'PropertyController@update', 'as' => 'propertyUpdate']);
            $router->delete('/delete/{id}', ['uses' => 'PropertyController@destroy', 'as' => 'propertyDelete']);
            $router->get('/employee-list-by-property/{id}', ['uses' => 'PropertyController@employeeList', 'as' => 'assignList']);
            $router->get('/assign-property', ['uses' => 'PropertyController@assignProperty', 'as' => 'propertyAssign']);
        });
        /**
         * EmployeeController
         */
        $router->group(['EmployeeController', 'prefix' => 'employee'], function () use ($router) {
            $router->get('/list', ['uses' => 'EmployeeController@index', 'as' => 'employeeList']);
            $router->post('/store', ['uses' => 'EmployeeController@store', 'as' => 'employeeStore']);
            $router->post('/profile/{id}', ['uses' => 'EmployeeController@employeeProfileImage', 'as' => 'employeeProfile']);
            $router->get('/edit/{id}', ['uses' => 'EmployeeController@edit', 'as' => 'employeeEdit']);
            $router->put('/update/{id}', ['uses' => 'EmployeeController@update', 'as' => 'employeeUpdate']);
            $router->delete('/delete/{id}', ['uses' => 'EmployeeController@destroy', 'as' => 'employeeDelete']);
        });
    });
        
    /**
     * SuperAdmin Routes
     */
    $router->group(['SuperAdmin', 'middleware' => 'authorize:'.UserType::SUPER_ADMIN], function() use ($router){
        /**
         * CompanyController
         */
        $router->group(['CompanyController', 'prefix' => 'company'],function() use ($router) {
            $router->get('/list', ['uses' => 'CompanyController@index', 'as' => 'companyList']);
            $router->post('/store', ['uses' => 'CompanyController@store', 'as' => 'companyStore']);
        });
        /**
         * CategoryController
         */
        $router->group(['CategoryController', 'prefix' => 'category'], function () use ($router) {
            $router->post('/store', ['uses' => 'CategoryController@store', 'as' => 'categoryStore']);
            $router->get('/show/{id}', ['uses' => 'CategoryController@show', 'as' => 'categoryShow']);
            $router->get('/edit/{id}', ['uses' => 'CategoryController@edit', 'as' => 'categoryEdit']);
            $router->put('/update/{id}', ['uses' => 'CategoryController@update', 'as' => 'categoryUpdate']);
            $router->delete('/delete/{id}', ['uses' => 'CategoryController@destroy', 'as' => 'categoryDelete']);
        });
        /**
         * CustomerController
         */
        $router->group(['CustomerController', 'prefix' => 'customer'],function() use ($router) {
            $router->get('/list', ['uses' => 'CustomerController@index', 'as' => 'customerList']);
            // $router->delete('/delete/{id}', ['uses' => 'CustomerController@destroy', 'as' => 'customerDelete']);
            // $router->post('/payment', ['uses' => 'CustomerController@paymentProcess', 'as' => 'paymentProcess']);
        });
        
    });


    /**
     * Customer Routes
     */
    $router->group(['Customer', 'middleware' => 'authorize:'.UserType::CUSTOMER], function() use ($router){
        /**
         * CustomerController
         */
        $router->group(['CustomerController', 'prefix' => 'customer'],function() use ($router) {
            // $router->post('/store', ['uses' => 'CustomerController@store', 'as' => 'customerStore']);
            $router->post('/profile/{id}', ['uses' => 'CustomerController@customerProfileImage', 'as' => 'customerPhoto']);
            $router->get('/edit/{id}', ['uses' => 'CustomerController@edit', 'as' => 'customerEdit']);
            $router->post('/update/{id}', ['uses' => 'CustomerController@update', 'as' => 'customerUpdate']);
            $router->delete('/delete/{id}', ['uses' => 'CustomerController@destroy', 'as' => 'customerDelete']);
        //    $router->post('/payment', ['uses' => 'CustomerController@paymentProcess', 'as' => 'paymentProcess']);

            
            
        });

        $router->get('/profile', ['uses' => 'AuthController@profile' , 'as' => 'userProfile']);
        $router->post('/update-profile', ['uses' => 'AuthController@updateProfile' , 'as' => 'updateProfile']);
         $router->post('/payment', ['uses' => 'CustomerController@paymentProcess', 'as' => 'paymentProcess']);

    });

    /**
     * Super Employee
     */
    $router->group(['SuperEmployee', 'middleware' => 'authorize:'.UserType::SUPER_EMPLOYEE], function() use ($router){
    });
});
