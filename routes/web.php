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

Route::get('/', function () {
    return view('welcome');
});

//Route Authenticate Admins
Route::post("authenticate/admin","UserAdminsController@authenticate");

//Route Authenticate Clients
Route::post("authenticate/client","UserClientsController@authenticate");


Route::post("register","UserClientsController@register");

Route::get("verify/account","UserClientsController@verifyActivation");

Route::get("activation/resend","UserClientsController@resendActivation");