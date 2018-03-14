<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route to verify if the token are still valid
Route::group(['prefix' => 'token','middleware' => ['jwt.auth']], function () {
    
    Route::get('verify', function () {
        
        return response()->json(['status' => 200, 'message' => "Token is valid."]);
    });

});




//Route Authenticate Admins
Route::post("authenticate/admin","UserAdminsController@authenticate");

//Route Authenticate Clients
Route::post("authenticate/client","UserClientsController@authenticate");

//Route for Client Registration and verification
Route::post("register","UserClientsController@register");

Route::get("verify/account","UserClientsController@verifyActivation");

Route::get("activation/resend","UserClientsController@resendActivation");

Route::post("activation/resend","UserClientsController@resendActivationMobile");

//Route Upload Valid ID for All Users

Route::post("users/upload/valid/id","UserClientsController@uploadValidId");

Route::post("users/change/address","UserClientsController@changeAddress");

//Route Sales and Admin
Route::group(['prefix' => 'sales','middleware' => 'jwt.auth'], function () {

    //Route for getting the pending, active and rejected inquiry of clients
    Route::group(['prefix' => 'inquire'], function () {

        Route::get("pending","InquiryController@getPending");

        Route::get("active","InquiryController@getActive");

        Route::get("rejected","InquiryController@getRejected");
        
    });

    //Route for accepting and rejecting client inquiry
    Route::group(['prefix' => 'inquiry'], function () {

        Route::post("rejecting","InquiryController@rejectInquiry");

        Route::post("accepting","InquiryController@acceptInquiry");

        Route::get("sales/{status}","InquiryController@getInquirySales");
    });

    //Route for uploading the created quotation for the client pdf only
    Route::group(['prefix' => 'quotations'], function () {
        
        Route::post('upload',"QuotaionController@uploadQuotation");
    });
    
});

//Routes for Cllients
Route::group(['prefix' => 'clients','middleware' => 'jwt.auth'], function () {

    //Route for inquire and getting the pending and onProcess inquiry for client
    Route::group(['prefix' => 'inquiry'], function () {

        Route::get("inquire","InquiryController@inquire");

        Route::get("pending","InquiryController@getInquiryClientPeding");

        Route::get("onProcess","InquiryController@getInquiryClientOnProcess");
        
    });

    Route::group(['prefix' => 'quotations'], function () {

        Route::post("accepting","QuotaionController@acceptQuotation");

        Route::post("rejecting","QuotaionController@rejectQuotation");
        
    });

    
});










