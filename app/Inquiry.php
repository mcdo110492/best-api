<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $table = "inquiry";

    protected $primaryKey = "inquiryId";

    protected $fillable = [
        "clientNumber",
        "clientId",
        "status",
        "dateConfirmed",
        "adminId",
        "remarks"
    ];
}
