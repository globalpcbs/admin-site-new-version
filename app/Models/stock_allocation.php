<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class stock_allocation extends Model
{
    //
    protected $table = "stock_allocation";
    protected $primaryKey = "id";
    public $timestamps = false;
    public function stock(){
        return $this->belongsTo(stock_tb::class,'stock_id');
    }
}