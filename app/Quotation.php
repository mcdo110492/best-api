<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = "quotation";
    
    protected $primaryKey = "quotationId";

    protected $fillable = [
        "inquiryId",
        "quotationFile",
        "dateQuotation",
        "status",
        "dateStatus",
        "remarks"
    ];
}
