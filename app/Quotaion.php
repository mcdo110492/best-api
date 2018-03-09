<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotaion extends Model
{
    protected $table = "quotation";
    
    protected $primaryKey = "quotationId";

    protected $fillable = [
        "inquiryId",
        "quotationFile",
        "status",
        "dateStatus",
        "remarks"
    ];
}
