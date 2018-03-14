<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Mail;

use App\Mail\AccountConfirmation;
use App\User;
use App\UserConfirmation;

use JWTAuth;
use Carbon\Carbon;

class UserClientsController extends Controller
{

    private $table;

    public function __construct(){

        $this->table = "users";

    }
    
    public function authenticate(Request $request){
        $credentials = $request->validate([
            'email'     =>  'required',
            'password'  =>  'required'
        ]);

            $table = $this->table;

            $where = ['email' => $credentials['email']];
       

            $checkEmail = DB::table($table)->where($where)->count();

            if($checkEmail > 0){

                 //Check if the account has been activated or not
                $checkIsConfirm = $this->isConfirm($credentials['email']);

                if($checkIsConfirm > 0){

                    $getUser = DB::table($table)->where($where)->get()->first();

                    $hashPassword = $getUser->password;
                    $userPassword = $credentials['password'];

                    if(Hash::check($userPassword, $hashPassword)){

                        $userData = [
                            'fullName'          =>  $getUser->fullName,
                            'userId'            =>  $getUser->userId,
                            "email"             =>  $getUser->email,
                            "refreshToken"      =>  str_random(),
                            "profilePicture"    =>  $getUser->profilePicture,
                            "contactNumber"     =>  $getUser->contactNumber
                        ];
                    
                        $user = (object) $userData;

                        $customClaims = ['userId' => $getUser->userId, 'role' => 3, 'email' => $getUser->email];

                        $token = JWTAuth::fromUser($user,$customClaims);

                        DB::table($table)->where($where)->update(['refreshToken' => $userData['refreshToken']]);

                        return response()->json(['status' => 200, 'token' => $token, 'user' => $userData, 'message' => "Valid"]);

                    }

                    return response()->json(['status' => 404, 'message' => "Invalid Credentials."]);

                }


                
                return response()->json(['status' => 403, 'message' => "Account is not activated."]);

            }

            return response()->json(['status' => 404, 'message' => "Invalid Credentials."]);


    }

    public function isConfirm($email){
        $table = $this->table;
        $check = DB::table($table)->where(['email' => $email])->where(['status' => 1])->count();

        return $check;
    }

    public function register(Request $request) {
        $table = $this->table;

        $credentials = $request->validate([
            'email'         =>  'required|email|unique:'.$table.',email',
            'password'      =>  'required',
            'fullName'      =>  'required',
            'contactNumber' =>  'required'
        ]);

        DB::transaction(function () use ($table,$credentials) {

            $registerData = [
                'email'         =>  $credentials['email'],
                'password'      =>  Hash::make($credentials['password']),
                'fullName'      =>  $credentials['fullName'],
                'contactNumber' =>  $credentials['contactNumber'],
                'role'          =>  3
            ];

            $create = User::create($registerData);

        
            $id = $create->userId;
    
            $exp = Carbon::now()->addDay();
    

            $createActivationCode = $this->createActivationCode($credentials['email'],$id);
    
            $data = ['userId' => $id, 'token' => $createActivationCode, 'expiredAt' => $exp, 'isConfirm' => 0];
    
            UserConfirmation::create($data);

            
        });

        $this->sendMail($credentials['email'],$this->token);
    
        
        return response()->json(['status' => 200, 'message' => "Register Successfully."]);

    }

    public function verifyActivation(Request $request){
        $token = $request['token'];
        $table = "userConfirmation";

        try {
            $decrypted = decrypt($token);

            $id     = $decrypted['id'];
            $hash   = $decrypted['hash'];

            $checkifClientExists = DB::table($table)->where(['userId' => $id])->count();

            if($checkifClientExists == 0){
                return view('account-confirmation',['status' => 404, 'message' => "The page that you're looking for cannot be found" ]);
            }

            $getData = DB::table($table)->where(['userId' => $id])->get()->first();

            $expired = Carbon::parse($getData->expiredAt)->isPast();

            if(!$expired){

                $checkIsAlreadyActivated = DB::table($table)->where(['confirmId' => $getData->confirmId, 'isConfirm' => 1])->count();

                if($checkIsAlreadyActivated == 0){

                    if($getData->token === $hash){

                        DB::transaction(function () use ($table,$getData) {
                            
                            DB::table($table)->where(['confirmId' => $getData->confirmId])->update(['isConfirm' => 1]);
                            DB::table('users')->where(['userId' => $getData->userId])->update(['status' => 1]);

                        });

                        return view('account-confirmation',['status' => 200, 'message' => "You' re account has been successfully activated."]);
                       
                    }
                }

                return view('account-confirmation',['status' => 402, 'message' => "Sorry. This activation code is expired" ]);

            }

            return view('account-confirmation',['status' => 401, 'message' => "Sorry. This activation code is expired", 'email' => $getData->email]);
            
            

        } catch (DecryptException $e) {
            
            return view('account-confirmation',['status' => 402, 'message' => "Sorry. This activation code is invalid"]);
        }
    }



    public function resendActivation(Request $request){

        $credentials = $request->validate([
            'email'    =>  'required'
        ]);

        $email = $credentials['email'];
    
        $checkEmailifExist = DB::table("users")->where(['email' => $email])->count();

        if($checkEmailifExist > 0){

            $getUser = DB::table("users")->where(['email' => $email])->get()->first();

            $id = $getUser->userId;
            
            $checkifAlreadyConfirm = DB::table('userConfirmation')->where(['userId' => $id, 'isConfirm' => 1])->count();

            if($checkifAlreadyConfirm == 0){

                $createActivationCode = $this->createActivationCode($getUser->email,$id);

                $exp = Carbon::now()->addDay();

                DB::table('userConfirmation')->where(['userId' => $id])->update(['token' => $createActivationCode, 'expiredAt' => $exp]);

                $this->sendMail($getUser->email,$this->token);

                $response = ['status' => 200, 'message' => "Activation Code has been sent to your email. Please wait for few minutes. Sometimes it will take a while to receive the email. Thank You"];

                return view('confirmation-resend',$response);

            }
            
            $response = ['status' => 403, 'message' => "Account already activated."];

            return view('confirmation-resend',$response);
        }

        $response = ['status' => 404, "message" => "404 not found. The page you' re looking for cannot be found."];

        return view('confirmation-resend',$response);

    }

    public function resendActivationMobile(Request $request){

        $credentials = $request->validate([
            'email'    =>  'required'
        ]);

        $email = $credentials['email'];
    
        $checkEmailifExist = DB::table("users")->where(['email' => $email])->count();

        if($checkEmailifExist > 0){

            $getUser = DB::table("users")->where(['email' => $email])->get()->first();

            $id = $getUser->userId;
            
            $checkifAlreadyConfirm = DB::table('userConfirmation')->where(['userId' => $id, 'isConfirm' => 1])->count();

            if($checkifAlreadyConfirm == 0){

                $createActivationCode = $this->createActivationCode($getUser->email,$id);

                $exp = Carbon::now()->addDay();

                DB::table('userConfirmation')->where(['userId' => $id])->update(['token' => $createActivationCode, 'expiredAt' => $exp]);

                $this->sendMail($getUser->email,$this->token);

                $response = ['status' => 200, 'message' => "Activation Code has been sent to your email. Please wait for few minutes. Sometimes it will take a while to receive the email. Thank You"];

               return response()->json($response);

            }
            
            $response = ['status' => 403, 'message' => "Account already activated."];

            return response()->json($response);
        }

        $response = ['status' => 404, "message" => "Email does not exist."];

        return response()->json($response);

    }

    public function sendMail($email,$token){

        $mail = Mail::to($email)->send(new AccountConfirmation($token));
 
 
    }

    public function createActivationCode($email, $id){

        $claims = [
            'email' =>  $email,
            'id'    =>  $id,
            "hash"  =>  str_random()
        ];

        $this->token = encrypt($claims);

        return $claims['hash'];

    }

    public function uploadValidId(Request $request){

        $credentials = $request->validate([
            "validId"   =>  "mimes:jpeg,jpg|max:5000"
        ]);

        $token = JWTAuth::parseToken()->authenticate();
        
        $userId = $token->userId;

        $where = ['userId' => $userId];
        $table = "userValidId";

        $file = $request->file("validId");

        $path = $request->quotation->store("validIDs");

        $data = ['validIdPath' => $path];

        $checkifAlreadyHaveId = DB::table($table)->where($where)->count();
        //if has a record update the existing instead otherwise create new record
        if($checkifAlreadyHaveId > 0){
            DB::table($table)->where($where)->update($data);
        }
        else{
           $data = ['validIdPath' => $path, 'userId' => $userId];
           DB::table($table)->insert($data);
        }
        
        return response()->json(['status' => 200, 'message' => "ID has been uploaded successfully."]);
    }


    public function changeAddress(Request $request){

        $credentials = $request->validate([
            'street'    =>  "required",
            "city"      =>  "required",
            "province"  =>  "required"
        ]);
        
        $token = JWTAuth::parseToken()->authenticate();
        
        $userId = $token->userId;

        $table = "userAddress";
        $where = ['userId' => $userId];

        $data = ['street' => $credentials['street'], 'city' => $credentials['city'], 'province' => $credentials['province']];

        $checkifAddressExists = DB::table($table)->where($where)->count();
        //Check if the user has already a record update instead otherwise create a new record
        if($checkifAddressExists > 0){
            DB::table($table)->where($where)->update($data);
        }
        else{
            $data = ['street' => $credentials['street'], 'city' => $credentials['city'], 'province' => $credentials['province'], 'userId' => $userId];
            DB::table($table)->insert($data);
        }

        return response()->json(['status' => 200, 'message' => "Address has been saved."]);
    }


}
