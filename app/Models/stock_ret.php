<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class stock_ret extends Model
{
    //
    protected $table = "stock_ret";
    protected $primaryKey = "rid";
    public $timestamps = false;
    public function stock(){
        return $this->belongsTo(stock_tb::class,'stkid');
    }
}