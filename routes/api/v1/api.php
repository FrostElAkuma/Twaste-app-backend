<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api\V1'], function () {
        //Getting products
       Route::group(['prefix' => 'products'], function () {
        Route::get('popular', 'ProductController@get_popular_products');
         Route::get('recommended', 'ProductController@get_recommended_products');
         Route::post('updateRemaining', 'ProductController@update_remaining_products');
         Route::post('removeItem', 'ProductController@remove_item');
         Route::get('cartRemaining', 'ProductController@cart_remaining_products');
          //I added this for our new drinks entry that we added in the larvarel admin pannel
          //Route::get('drinks', 'ProductController@get_drinks');
           
           
    }); 
    
        //Registration and login
        Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function () {
        Route::post('register', 'CustomerAuthController@register');
        Route::post('login', 'CustomerAuthController@login');
        });

        //Restaurant info
        Route::group(['prefix' => 'restaurant'], function () {
            Route::get('info', 'RestaurantController@get_info');
            //~5 hours just to realize I had a typo in below line :DD
            Route::post('upload', 'RestaurantController@upload');
            Route::post('new', 'RestaurantController@new');
            });
   
        //This is gaurded with 'middleware' => 'auth:api', It is used for security and it is pre built in lavarel, it makes sure that each user is treated differently and data does not match up
        Route::group(['prefix' => 'customer', 'middleware' => 'auth:api'], function () {
            Route::get('notifications', 'NotificationController@get_notifications');
            Route::get('info', 'CustomerController@info');
            Route::post('update-profile', 'CustomerController@update_profile');
            Route::post('update-interest', 'CustomerController@update_interest');
            Route::put('cm-firebase-token', 'CustomerController@update_cm_firebase_token');
            Route::get('suggested-foods', 'CustomerController@get_suggested_food');

        Route::group(['prefix' => 'address'], function () {
            Route::get('list', 'CustomerController@address_list');
            Route::post('add', 'CustomerController@add_new_address');
            Route::post('update', 'CustomerController@update_address');
            Route::delete('delete', 'CustomerController@delete_address');
        });
                Route::group(['prefix' => 'order'], function () {
            Route::get('list', 'OrderController@get_order_list');
            Route::get('running-orders', 'OrderController@get_running_orders');
            Route::get('details', 'OrderController@get_order_details');
            Route::post('place', 'OrderController@place_order');
            Route::put('cancel', 'OrderController@cancel_order');
            Route::put('refund-request', 'OrderController@refund_request');
            Route::get('track', 'OrderController@track_order');
            Route::put('payment-method', 'OrderController@update_payment_method');
        });
            });
            
        Route::group(['prefix' => 'config'], function () {
        Route::get('/', 'ConfigController@configuration');
        //need to remove the / ?
        Route::get('/get-zone-id', 'ConfigController@get_zone');
        Route::get('place-api-autocomplete', 'ConfigController@place_api_autocomplete');
        Route::get('distance-api', 'ConfigController@distance_api');
        Route::get('place-api-details', 'ConfigController@place_api_details');
        Route::get('geocode-api', 'ConfigController@geocode_api');
    });
});
