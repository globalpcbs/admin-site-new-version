<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class items_tb extends Model
{
    //
    protected $table = 'items_tb';
    protected $primaryKey = 'item_id';
    public $timestamps = false;
    protected $fillable = [
        'item',
        'itemdesc',
        'qty2',
        'uprice',
        'tprice',
        'pid',
        'dpval',
    ];
    public function porder(){
        return $this->belongsTo(porder_tb::class,'pid');
    }
}