<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class data_tb extends Model
{
    //
    protected $table = 'data_tb';
    protected $primaryKey = 'data_id';
    public $timestamps = false;

    protected $fillable = [
        'c_name', 'c_shortname', 'c_email', 'c_address', 'c_address2', 'c_address3',
        'c_phone', 'c_fax', 'c_website', 'c_bcontact', 'e_name', 'e_lname',
        'e_phone', 'e_email', 'e_payment', 'e_comments', 'e_other', 'e_cid'
    ];
}
