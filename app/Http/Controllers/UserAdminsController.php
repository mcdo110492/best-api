<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\User;

use JWTAuth;
use JWTFactory;

class UserAdminsController extends Controller
{
    


    public function authenticate(Request $request){

        $credentials = $request->validate([
            'email'     =>  'required',
            'password'  =>  'required'
        ]);

        $where = ['email'   =>  $credentials['email']];

        $table = "users";
        
        
        
       

            //Chekc if email exists in the database
            $checkUsername = DB::table($table)->where($where)->count();

            if($checkUsername > 0 ) {

                //Check Status if the user is active
                $checkStatus = DB::table($table)->where($where)->where(['status' => 1])->count();

                if($checkStatus > 0){

                    $getUser = DB::table($table)->where($where)->get()->first();

                    //Check Role only role with 1 and 2
                    $role = $getUser->role;
                    if($role == 3){

                        return response()->json(['status' => 403, "message" => "You' re not authorized."]);

                    }

                    $hashPassword = $getUser->password;
                    $userPassword = $credentials['password'];

                    //Check if the user input password match the hash password in the database
                    if(Hash::check($userPassword, $hashPassword)){

                        $userData = [
                            'fullName'          =>  $getUser->fullName,
                            'userId'            =>  $getUser->userId,
                            "email"             =>  $getUser->email,
                            "role"              =>  $getUser->role,
                            "refreshToken"      =>  $getUser->refreshToken,
                            "profilePicture"    =>  $getUser->profilePicture,
                            "contactNumber"     =>  $getUser->contactNumber
                        ];

                        $customClaims = ['userId' => $getUser->userId, 'role' => $getUser->role, 'email' => $getUser->email];

                        $user = (object) $userData;

                        $token = JWTAuth::fromUser($user,$customClaims);

                        return response()->json(['status' => 200, 'token' => $token, 'user' => $userData, 'message' => "Valid"]);

                    }

                    //Response a status of 404 if the email or password is valid
                    return response()->json(['status' => 404, 'message' => "Email or Password is Invalid."]);

                }

                 //Response a status 403 if the account has been disabled
                return response()->json(['status' => 403, 'message' => "Account is Disabled."]);

            }

            //Response a status of 404 if the email or password is valid
            return response()->json(['status' => 404, 'message' => "Email or Password is Invalid."]);


    }

}
