<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class citems_tb extends Model
{
    //
    protected $table = "citems_tb";
    protected $primaryKey = 'item_id';
    public $timestamps = false;
    protected $fillable = [
        'item',
        'itemdesc',
        'qty2',
        'uprice',
        'tprice',
        'pid',
    ];
    
}