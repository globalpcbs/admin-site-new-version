<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class rep_tb extends Model
{
    //
    protected $table = "rep_tb";
    protected $primaryKey = 'repid';
    public $timestamps = false;
    protected $fillable = [
        'r_name', 'c_name', 'c_email', 'c_address', 'c_address2', 'c_address3',
        'c_phone', 'c_fax', 'c_website', 'e_payment', 'e_comments',
        'indirect', 'invsoldto', 'comval'
    ];
}
