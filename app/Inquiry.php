<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Inquiry extends Model
{
    protected $table = "inquiry";

    protected $primaryKey = "inquiryId";

    protected $fillable = [
        "userId",
        "status",
        "dateInquire",
        "dateConfirmed",
        "adminId",
        "remarks"
    ];



    public function details(){

        return $this->hasOne('App\InquiryDetails','inquiryId');

    }

    public function quotations() {

        return $this->hasOne('App\Quotation','inquiryId');

    }

    public function admin() {

        return $this->hasOne('App\User','userId','adminId');

    }


}
