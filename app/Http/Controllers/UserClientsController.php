<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Mail\AccountConfirmation;
use Illuminate\Support\Facades\Mail;

use App\UserClients;
use App\UserClientConfirmation;

use JWTAuth;
use Carbon\Carbon;

class UserClientsController extends Controller
{

    private $table;

    public $token;

    public function __construct(){

        $this->table = "userClients";

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
                            'id'                =>  $getUser->clientId,
                            "email"             =>  $getUser->email,
                            "refreshToken"      =>  $getUser->refreshToken,
                            "profilePicture"    =>  $getUser->profilePicture
                        ];
                    
                        $user = (object) $userData;

                        $customClaims = ['userId' => $getUser->clientId, 'role' => 3, 'email' => $getUser->email];

                        $token = JWTAuth::fromUser($user,$customClaims);

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
            'email'         =>  'required|unique:'.$table.',email',
            'password'      =>  'required',
            'fullName'      =>  'required',
            'contactNumber' =>  'required'
        ]);

        DB::transaction(function () use ($table,$credentials) {

            $create = UserClients::create($credentials);

        
            $id = $create->clientId;
    
            $exp = Carbon::now()->addDay();
    

            $createActivationCode = $this->createActivationCode($credentials['email'],$id);
    
            $data = ['clientId' => $id, 'token' => $createActivationCode, 'expiredAt' => $exp, 'isConfirm' => 0];
    
            UserClientConfirmation::create($data);

            
        });

        $this->sendMail($credentials['email'],$this->token);
    
        
        return response()->json(['status' => 200, 'message' => "Register Successfully.", 'token']);

    }

    public function verifyActivation(Request $request){
        $token = $request['token'];
        $table = "userClientConfirmation";

        try {
            $decrypted = decrypt($token);

            $id     = $decrypted['id'];
            $hash   = $decrypted['hash'];

            $getData = DB::table($table)->where(['clientId' => $id])->get()->first();

            $expired = Carbon::parse($getData->expiredAt)->isPast();

            if(!$expired){

                $checkIsAlreadyActivated = DB::table($table)->where(['confirmId' => $getData->confirmId, 'isConfirm' => 1])->count();

                if($checkIsAlreadyActivated == 0){

                    if($getData->token === $hash){

                        DB::transaction(function () use ($table,$getData) {
                            
                            DB::table($table)->where(['confirmId' => $getData->confirmId])->update(['isConfirm' => 1]);
                            DB::table('userClients')->where(['clientId' => $getData->clientId])->update(['status' => 1]);

                        });

                        return view('account-confirmation',['status' => 200, 'message' => "You' re account has been successfully activated.", 'id' => $id]);
                       
                    }
                }

                return view('account-confirmation',['status' => 402, 'message' => "Sorry. This activation code is expired" ]);

            }

            return view('account-confirmation',['status' => 401, 'message' => "Sorry. This activation code is expired"]);
            
            

        } catch (DecryptException $e) {
            
            return view('account-confirmation',['status' => 402, 'message' => "Sorry. This activation code is invalid"]);
        }
    }



    public function resendActivation(Request $request){

        $credentials = $request->validate([
            'id'    =>  'required'
        ]);

        $id = $credentials['id'];
    
        $checkIdifExist = DB::table("userClients")->where(['clientId' => $id])->count();

        if($checkIdifExist > 0){
            
            $checkifAlreadyConfirm = DB::table('userClientConfirmation')->where(['clientId' => $id, 'isConfirm' => 1])->count();

            if($checkifAlreadyConfirm == 0){

                $getUser = DB::table("userClients")->where(['clientId' => $id])->get()->first();

                $createActivationCode = $this->createActivationCode($getUser->email,$id);

                DB::table('userClientConfirmation')->where(['clientId' => $id])->update(['token' => $createActivationCode]);

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


}
