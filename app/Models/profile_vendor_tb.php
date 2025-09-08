<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\profile_vendor_tb2 as ProfileVendor2;

class profile_vendor_tb extends Model
{
    //
    protected $table = 'profile_vendor_tb';
    protected $primaryKey = 'profid';
    public $timestamps = false;

    protected $fillable = ['custid'];

    public function requirements()
    {
        return $this->hasMany(ProfileVendor2::class, 'profid', 'profid');
    }
    
    public function vendor()
    {
        return $this->belongsTo(vendor_tb::class, 'custid', 'data_id');
    }
}
