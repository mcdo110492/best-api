<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $table = "inquiry";

    protected $primaryKey = "inquiryId";

    protected $fillable = [
        "userId",
        "status",
        "dateConfirmed",
        "adminId",
        "remarks"
    ];


    public function getInquiryIdAttribute($value){

        $zeroFill = 0000000000;
        return ($zeroFill + $value);
    }
}
