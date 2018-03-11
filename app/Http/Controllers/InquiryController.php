<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Inquiry;
use App\InquiryDetails;
use App\User;
use App\Events\InquiryEvent;
use App\Events\InquiryClient;

use JWTAuth;
use Carbon\Carbon;

class InquiryController extends Controller
{

    protected $user;

    public function __construct() {

        $this->user = JWTAuth::parseToken()->authenticate();

    }

   
    /**
     * Method to inquire for clients only
     */
    public function inquire(){

        $role = $this->user->role;

        if($role != 3){
            
            return response()->json(['status' => 403, 'message' => "You 're not authorized."]);
        }

        $clientId = $this->user->userId;

        $checkAddress = $this->checkClientAddress($clientId);
        $checkValidID = $this->checkClientValidId($clientId);

        if($checkAddress['status'] == 404 || $checkValidID['status'] == 404){

            return response()->json(['status' => 404, 'message' => "You' re address and valid ID is not set."]);
        }
        


        DB::transaction(function () use ($clientId,$checkAddress,$checkValidID) {
           
            $user    = $this->getUserData($clientId);
            $address = $checkAddress['data'];
            $validId = $checkValidID['data'];
    
            $inquireData = [
                'userId'        =>  $clientId,
                'status'        =>  0,
                'dateInquire'   => Carbon::now()
            ];
    
            $createInquiry = Inquiry::create($inquireData);

            $inquiryId = $createInquiry->inquiryId;

            $inquireDetailsData = [
                'inquiryId'     =>  $inquiryId,
                'clientNumber'  =>  str_pad($inquiryId, 10, '0', STR_PAD_LEFT),
                'email'         =>  $user->email,
                'fullName'      =>  $user->fullName,
                'contactNumber' =>  $user->contactNumber,
                'street'        =>  $address->street,
                'city'          =>  $address->city,
                'province'      =>  $address->province,
                'validIdPath'   =>  $validId->validIdPath
            ];

            InquiryDetails::create($inquireDetailsData);

            $inquiryDate = Carbon::parse($createInquiry->dateInquire)->toDayDateTimeString();

            event(new InquiryEvent($inquiryId,$user->fullName,$user->email,$inquiryDate,$inquireDetailsData['clientNumber']));

        });
       
        return response()->json(['status' => 200, 'message' => "Inquiry Sent."]);
        
    }

    /**
     * Check Client Address Function before requesting inuiry
     */
    public function checkClientAddress($id){
        $table = "userAddress";
        $where = ['userId' => $id];

        $checkClientAddress = DB::table($table)->where($where)->count();

        if($checkClientAddress > 0){

            $data = DB::table($table)->where($where)->get()->first();

            return ['status' => 200, 'data' => $data];

        }

    
        return ['status' => 404];

    }

    /**
     * Check Client Valid Id Function before requesting inquiry
     */
    public function checkClientValidId($id){

        $table = "userValidID";
        $where = ['userId' => $id];

        $check = DB::table($table)->where($where)->count();

        if($check > 0){

            $data = DB::table($table)->where($where)->get()->first();

            return ['status' => 200, 'data' => $data];
        }

        return ['status' => 404];

    }

    /**
     * Get the Client Data Function
     */
    public function getUserData($id){
        $table = "users";
        $where = ['userId' => $id];
        $data = DB::table($table)->where($where)->get()->first();
        return $data;
    }

    /**
     * Get All the Pending Inquiry 
     * Admin and Sales only
     * broadcast to App\Events\InquiryEvent
     */
    public function getPending(){
        $where = ['status' => 0];
        $q = Inquiry::with('details')->where($where);
        $get = $q->get();
        $count = $q->count();
        
        return response()->json(['count' => $count,'data' => $get]);

    }

    /**
     * Get All the Active Inquiry
     * Admin and Sales only
     * Depending who (Sales) accepted the inquiry
     * broadcast to App\Events\InquiryEvent
     */
    public function getActive(){
        $adminId = $this->user->userId;
        $where = ['status' => 1, 'adminId' => $adminId];
        $get = Inquiry::with('details')->where($where)->get();
        $count = $get->count();
        
        return response()->json(['count' => $count,'data' => $get]);
    }   

     /**
     * Get All the Active Inquiry
     * Admin and Sales only
     * Depending who (Sales) rejected the inquiry
     * broadcast to App\Events\InquiryEvent
     */
    public function getRejected(){
        $adminId = $this->user->userId;
        $where = ['status' => 2, 'adminId' => $adminId];
        $get = Inquiry::with('details')->where($where)->get();
        $count = $get->count();
        
        return response()->json(['count' => $count,'data' => $get]);
    }

    /**
     * Accept Inquiry Method
     * Sales and Admin only
     * Broadcast to event App\Events\InquiryClient
     */
    public function acceptInquiry(Request $request){
        $role = $this->user->role;
        if($role == 3){
            return response()->json(['status' => 403, 'message' => "You 're not authorized."]);
        }
        $adminId = $this->user->userId;

        $credentials = $request->validate([
            'inquiryId' =>  'required',
            'clientId'  =>  'required'
        ]);

        $inquiryId = $credentials['inquiryId'];

        $data = [
            'adminId'       =>  $adminId,
            'dateConfirmed' =>  Carbon::now(),
            'status'        =>  1
        ];

        $q = Inquiry::findOrFail($inquiryId);

        $q->update($data);

        event(new InquiryClient($credentials['clientId'],$inquiryId,$adminId));

        return response()->json(['status' => 200, 'message' => "Inquiry Accepted."]);
    }

     /**
     * Reject Inquiry Method
     * Sales and Admin only
     * Broadcast to event App\Events\InquiryClient
     */
    public function rejectInquiry(Request $request){
        $role = $this->user->role;
        if($role == 3){
            return response()->json(['status' => 403, 'message' => "You 're not authorized."]);
        }
        $adminId = $this->user->userId;

        $credentials = $request->validate([
            'inquiryId' =>  'required',
            'clientId'  =>  'required',
            'remarks'   =>  'required'
        ]);

        $inquiryId = $credentials['inquiryId'];

        $data = [
            'adminId'       =>  $adminId,
            'dateConfirmed' =>  Carbon::now(),
            'status'        =>  2,
            'remarks'       =>  $credentials['remarks']
        ];

        $q = Inquiry::findOrFail($inquiryId);

        $q->update($data);

        event(new InquiryClient($credentials['clientId'],$inquiryId,$adminId));

        return response()->json(['status' => 200, 'message' => "Inquiry Rejected."]);
    }

}
