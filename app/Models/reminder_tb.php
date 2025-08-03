<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reminder_tb extends Model
{
    //
    protected $table = "reminder_tb";
    protected $primaryKey = 'id';
    public $timestamps = false;
        protected $fillable = [
        'quoteid',
        'enabled',
        'days',
        'lastreminder',
    ];
     public function order()
    {
        return $this->belongsTo(order_tb::class, 'quoteid', 'ord_id');
    }

}