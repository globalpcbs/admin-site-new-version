<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\data_tb as Customer;
use App\Models\profile_tb as Profile;
use App\Models\profile_tb2 as ProfileDetail;

class profile_tb extends Model
{
    //
    protected $table = 'profile_tb';
    protected $primaryKey = 'profid';
    public $timestamps = false;
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'custid', 'data_id');
    }

    public function details()
    {
        return $this->hasMany(ProfileDetail::class, 'profid', 'profid');
    }
}
