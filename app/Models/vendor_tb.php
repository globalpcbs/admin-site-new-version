<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vendor_tb extends Model
{
    //
     protected $table = "vendor_tb";
    public $timestamps = false;
    protected $primaryKey = 'data_id';
     protected $fillable = [
        'c_name',
        'c_shortname',
        'c_address',
        'c_address2',
        'c_address3',
        'c_phone',
        'c_fax',
        'c_website',
        'e_payment',
        'e_comments',
        'e_other',
    ];
}
