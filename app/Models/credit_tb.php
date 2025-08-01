<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\credit_items_tb as CreditItem;

class credit_tb extends Model
{
    //
    protected $table = 'credit_tb';
    protected $primaryKey = 'credit_id';
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
        'ord_by',
        'date1',
        'dweek',
        'po',
        'our_ord_num',
        'saletax',
        'no_layer',
        'credit_date',
        'sp_reqs',
    ];

    
    public function items()
    {
        return $this->hasMany(CreditItem::class, 'pid');
    }
    // shippers ..
    public function shipper() {
        return $this->belongsTo(shipper_tb::class,'sid');
    }
    // customer ..
    public function custo(){
        return $this->belongsTo(data_tb::class,'vid');
    }
    
}