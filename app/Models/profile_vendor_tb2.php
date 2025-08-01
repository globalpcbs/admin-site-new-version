<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class profile_vendor_tb2 extends Model
{
    //
    protected $table = 'profile_vendor_tb2';
    public $timestamps = false;

    protected $fillable = ['profid', 'reqs'];
}
