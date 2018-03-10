<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Inquiry;
use App\InquiryDetails;
use App\UserClients;

use JWTAuth;

class InquiryController extends Controller
{



   

    public function inquire(){

        $parse = JWTAuth::parseToken()->authenticate();

   
       // $client = UserClients::findOrFail($clientId);

        return response()->json(['clientName' => $parse ]);

    }
}
