<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationClient extends Model
{
    protected $table = "quotationClient";

    protected $primaryKey = "quotationClientId";

    protected $fillable = [
        'inquiryId',
        'pathFile'
    ];
}
