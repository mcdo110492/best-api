<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB; 


// Private Channel for Requesting an SOS
Broadcast::channel('inquiry-client.{clientId}', function(){

    $token =  $token = JWTAuth::parseToken()->authenticate();

    $role = $token->role;

    if($role == 3){
        return true;
    }

    return false;
});