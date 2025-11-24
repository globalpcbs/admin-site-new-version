<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class porder_tb extends Model
{
    //
    protected $table = "porder_tb";
    protected $primaryKey = "poid";
    public $timestamps = false;
    protected $fillable = [
        'vid',
        'sid',
        'namereq',
        'namereq1',
        'svia',
        'svia_oth',
        'city',
        'state',
        'sterms',
        'rohs',
        'comments',
        'podate',
        'customer',
        'part_no',
        'rev',
        'date1',
        'date2',
        'po',
        'dweek',
        'no_layer',
        'cancharge',
        'ordon',
        'iscancel',
        'ccharge',
        'sp_reqs',
    ];

    // items ...
    public function items(){
        return $this->hasMany(items_tb::class,'pid');
    }
    public function vendor()
    {
        return $this->belongsTo(vendor_tb::class, 'vid', 'data_id');
    }

}