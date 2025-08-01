<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class packing_items_tb extends Model
{
    //
    protected $table = "packing_items_tb";
    protected $primaryKey = 'item_id';
    public $timestamps = false;
    // packing ...
    protected $fillable = [
        'item',
        'itemdesc',
        'qty2',
        'shipqty',
        'pid', // foreign key to packing_tb (packing slip ID)
    ];
}