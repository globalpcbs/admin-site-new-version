<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class credit_items_tb extends Model
{
    //
    protected $table = 'credit_items_tb';
    protected $primaryKey = 'item_id';
    public $timestamps = false;
    protected $fillable = ['item','itemdesc','qty2','uprice','tprice','pid'];
}