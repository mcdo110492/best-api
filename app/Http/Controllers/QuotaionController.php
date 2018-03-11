<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Quotation;
use App\Events\QuotationClients;
use App\Events\QuotationSales;

use JWTAuth;
use Carbon\Carbon;

class QuotaionController extends Controller
{
    protected $user;

    public function __construct() {

        $this->user = JWTAuth::parseToken()->authenticate();

    }

    public function uploadQuotation(Request $request){

        if($request->hasFile('quotation')){

            $request->validate([
                'quotation'     =>  'mimes:pdf|max:5000',
                'inquiryId'     =>  'required'
            ]);
            
            $file = $request->file("quotation");
            $inquiryId = $request['inquiryId'];

            $path = $request->quotation->store("quotations");
            
            $currentData = Carbon::now();

            $data = ['inquiryId' => $inquiryId, 'quotationFile' => $path, 'dateQuotation' => $currentData];
            
           
            Quotation::create($data);
       

            $getUser= DB::table("inquiry")->select("userId")->where(['inquiryId' => $inquiryId])->get()->first();
            $userId = $getUser->userId;

            event(new QuotationClients($userId,$inquiryId));

            return response()->json(['status' => 200, 'message' => "Quotation Created Successfully."]);

        }

        return response()->json(['status' => 404, 'message' => "File not found."]);

    }

    /**
     * Method for accepting quotations for clients only
     * This method will complete the inquiry transactions
     */
    public function acceptQuotation(Request $request){

        $clientId = $this->user->userId;
        
        $credentials = $request->validate([
            'quotationId'   =>  'required'
        ]);
        
        $quotation = Quotation::findOrFail($credentials['quotationId']);
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

        $clientId = $this->user->userId;
        
        $credentials = $request->validate([
            'quotationId'   =>  'required'
        ]);
        
        $quotation = Quotation::findOrFail($credentials['quotationId']);
        $currentDate = Carbon::now();

        $data = ['status' => 2, 'dateStatus' => $currentDate];

        $quotation->update($data);

        $getInquiry = DB::table('inquiry as i')->select('i.inquiryId,i.adminId')->leftJoin("quotation as q")->where(['q.quotationId' => $credentials['quotationId']])->get()->first();
        
        $inquiryId = $getInquiry->inquiryId;
        $adminId = $getInquiry->adminId;

        event(new QuotationSales($inquiryId,2,$adminId));

        return response()->json(['status' => 200, 'message' => "Quotation has been accepted."]);

    }
}
