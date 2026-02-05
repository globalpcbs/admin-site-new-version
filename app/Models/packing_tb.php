<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class packing_tb extends Model
{
    //
    protected $table = "packing_tb";
    protected $primaryKey = 'invoice_id';
    public $timestamps = false;
    protected $fillable = [
        'vid',
        'sid',
        'namereq',
        'svia',
        'svia_oth',
        'fcharge',
        'city',
        'state',
        'sterm',
        'comments',
        'podate',
        'customer',
        'part_no',
        'rev',
        'delto',
        'date1',
        'odate',
        'dweek',
        'po',
        'our_ord_num',
        'saletax',
        'no_layer',
        'sp_reqs',
    ];
     // packing ...
    public function items(){
        return $this->hasMany(packing_items_tb::class,'pid');
    }
    // customer ..
    public function custo(){
        return $this->belongsTo(data_tb::class,'vid');
    }
    public function customer(){
        return $this->belongsTo(data_tb::class,'customer','data_id');
    }

}