<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Inquiry;
use App\Quotation;
use App\Events\QuotationClients;
use App\Events\QuotationSales;

use JWTAuth;
use Carbon\Carbon;

class QuotationController extends Controller
{
    protected $user;

    public function __construct() {

        $this->user = JWTAuth::parseToken()->authenticate();

    }
    /**
     * Method of uploading quotation Sales and Admin only
     */
    public function uploadQuotation(Request $request){

        $role = $this->user->role;

        if($role == 3){
            return response()->json(['status' => 403, "message" => "Unauthorized Access"]);
        }

        if($request->hasFile('quotation')){

            $request->validate([
                'quotation'     =>  'mimes:pdf|max:5000',
                'inquiryId'     =>  'required'
            ]);
            
            $file = $request->file("quotation");
            $inquiryId = $request['inquiryId'];

            //Check if inquiry exists in the database
            $q = Inquiry::findOrFail($inquiryId);

            $path = $request->quotation->store("quotations");

            DB::transaction(function () use ($path,$inquiryId,$q) {

                $currentData = Carbon::now();

                $data = ['inquiryId' => $inquiryId, 'quotationFile' => $path, 'dateQuotation' => $currentData];
                
               
                Quotation::create($data);

                $q->update(['status' => 3]);
    
                $getUser= DB::table("inquiry")->select("userId")->where(['inquiryId' => $inquiryId])->get()->first();
                $userId = $getUser->userId;

                event(new QuotationClients($userId,$inquiryId));

            });
            
        

            return response()->json(['status' => 200, 'message' => "Quotation Created Successfully."]);

        }

        return response()->json(['status' => 404, 'message' => "File not found."]);

    }

    /**
     * Method for accepting quotations for clients only
     * This method will complete the inquiry transactions
     */
    public function acceptQuotation(Request $request){

        $role = $this->user->role;

        if($role != 3){
            return response()->json(['status' => 403, "message" => "Unauthorized Access"]);
        }

        $clientId = $this->user->userId;
        
        $credentials = $request->validate([
            'quotationId'   =>  'required'
        ]);
        
        $quotation = Quotation::findOrFail($credentials['quotationId']);

        $checkIfYouOwn = DB::table('inquiry')->where(['inquiryId' => $quotation->inquiryId, 'userId' => $clientId])->count();

        if($count == 0 ){
            return response()->json(['status' => 403, "message" => "Unauthorized Access"]);
        }

        $currentDate = Carbon::now();

        $data = ['status' => 1, 'dateStatus' => $currentDate];

        $quotation->update($data);

        $getInquiry = DB::table('inquiry as i')->select('i.inquiryId,i.adminId')->leftJoin("quotation as q")->where(['q.quotationId' => $credentials['quotationId']])->get()->first();
        
        $inquiryId = $getInquiry->inquiryId;
        $adminId = $getInquiry->adminId;

        event(new QuotationSales($inquiryId,1,$adminId));

        return response()->json(['status' => 200, 'message' => "Quotation has been accepted."]);

    }

    /**
     * Method for rejecting quotations for clients only
     * This method will not complete until the client accepted the quotations
     */
    public function rejectQuotation(Request $request){

        $role = $this->user->role;

        if($role != 3){
            return response()->json(['status' => 403, "message" => "Unauthorized Access"]);
        }

        $clientId = $this->user->userId;
        
        $credentials = $request->validate([
            'quotationId'   =>  'required'
        ]);
        
        $quotation = Quotation::findOrFail($credentials['quotationId']);

        $checkIfYouOwn = DB::table('inquiry')->where(['inquiryId' => $quotation->inquiryId, 'userId' => $clientId])->count();

        if($count == 0 ){
            return response()->json(['status' => 403, "message" => "Unauthorized Access"]);
        }

        $currentDate = Carbon::now();

        $data = ['status' => 2, 'dateStatus' => $currentDate];

        $quotation->update($data);

        $getInquiry = DB::table('inquiry as i')->select('i.inquiryId,i.adminId')->leftJoin("quotation as q")->where(['q.quotationId' => $credentials['quotationId']])->get()->first();
        
        $inquiryId = $getInquiry->inquiryId;
        $adminId = $getInquiry->adminId;

        event(new QuotationSales($inquiryId,2,$adminId));

        return response()->json(['status' => 200, 'message' => "Quotation has been accepted."]);

    }

    /**
     * Method for getting the currently active or pending quotation  of the Clients
     */
    public function getClientQuotation(){
        $role = $this->user->role;

        if($role !== 3){
            return response()->json(['status' => 403, "message" => "Unauthorized Access"]);
        }

        $clientId = $this->user->userId;

        $q = Inquiry::with(['admin','details','quotations'])->where(['status' => 3, 'userId' => $clientId]);
        $inquiry = $q->get();
        $count = $q->count();

        return response()->json(['status' => 200, 'data' => $inquiry, 'count' => $count]);

    }

    /**
     * Method for upload a quotation of Client
     */
    public function uploadClientQuotation(Request $request){

        $role = $this->user->role;

        if($role != 3){
            return response()->json(['status' => 403, "message" => "Unauthorized Access"]);
        }

        if($request->hasFile('quotation')){

            $request->validate([
                'quotation'     =>  'mimes:pdf|max:5000',
                'inquiryId'     =>  'required'
            ]);
            
            $file = $request->file("quotation");
            $inquiryId = $request['inquiryId'];

            //Check if inquiry exists in the database
           // $q = Inquiry::findOrFail($inquiryId);

            $path = $request->quotation->store("quotations");

            // DB::transaction(function () use ($path,$inquiryId,$q) {

            //     $currentData = Carbon::now();

            //     $data = ['inquiryId' => $inquiryId, 'quotationFile' => $path, 'dateQuotation' => $currentData];
                
               
            //     Quotation::create($data);

            //     $q->update(['status' => 3]);
    
            //     $getUser= DB::table("inquiry")->select("userId")->where(['inquiryId' => $inquiryId])->get()->first();
            //     $userId = $getUser->userId;

            //     event(new QuotationClients($userId,$inquiryId));

            // });
            
        

            return response()->json(['status' => 200, 'message' => "Quotation Created Successfully."]);

        }

        return response()->json(['status' => 404, 'message' => "File not found."]);
    }
}
