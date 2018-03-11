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

//Route Authenticate Admins
Route::post("authenticate/admin","UserAdminsController@authenticate");

//Route Authenticate Clients
Route::post("authenticate/client","UserClientsController@authenticate");

//Route for Client Registration and verification
Route::post("register","UserClientsController@register");

Route::get("verify/account","UserClientsController@verifyActivation");

Route::get("activation/resend","UserClientsController@resendActivation");

//Route Upload Valid ID for All Users

Route::post("users/upload/valid/id","UserClientsController@uploadValidId");

Route::post("users/change/address","UserClientsController@changeAddress");


//Inquiry Route Clients
Route::post("inquire","InquiryController@inquire");

//Inquiry Route Sales
Route::get("inquiry/pending","InquiryController@getPending");

Route::get("inquiry/active","InquiryController@getActive");

Route::get("inquiry/rejected","InquiryController@getRejected");

//Inquiry Routes to accept and reject the inquiry Sales
Route::post('inquiry/accept',"InquiryController@acceptInquiry");

Route::post('inquiry/reject',"InquiryController@rejectInquiry");

//Quotation Routes Sales
Route::post('quotations/upload',"QuotaionController@uploadQuotation");


//Quotation Routes Clients
Route::post("quotations/accept","QuotaionController@acceptQuotation");

Route::post("quotations/reject","QuotaionController@rejectQuotation");