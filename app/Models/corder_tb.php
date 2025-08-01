<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class corder_tb extends Model
{
    //
    protected $table = "corder_tb";
    protected $primaryKey = 'poid';
    public $timestamps = false;
    protected $fillable = [
        'vid',
        'sid',
        'namereq',
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
        'our_ord_num',
        'mdl',
        'delto',
        'stax',
        'no_layer',
        'sp_reqs',
        'dweek',
    ];
    public function items(){
        return $this->hasMany(citems_tb::class,'pid');
    }
    public function deliveries()
    {
        return $this->hasMany(mdlitems_tb::class, 'pid', 'poid');
    }
    
}