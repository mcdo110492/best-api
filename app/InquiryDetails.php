<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InquiryDetails extends Model
{
    protected $table = "inquiryDetails";

    protected $primaryKey = "inquiryDetailsId";

    protected $fillable = [
        "inquiryId",
        "email",
        "fullName",
        "contactNumber",
        "street",
        "city",
        "province",
        "validIdPath"
    ];
}
