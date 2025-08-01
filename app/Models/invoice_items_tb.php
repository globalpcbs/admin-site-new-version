<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoice_items_tb extends Model
{
    //
    //
    protected $table = 'invoice_items_tb';
    protected $primaryKey = 'item_id';
    public $timestamps = false;
    
    protected $fillable = [
        'pid',
        'item',
        'description',
        'qty',
        'unit_price',
        'commision',
        'tprice',
    ];
}