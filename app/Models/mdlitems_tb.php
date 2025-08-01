<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class mdlitems_tb extends Model
{
    //
    protected $table = "mdlitems_tb";
    protected $primaryKey = 'item_id';
    public $timestamps = false;
    protected $fillable = [
        'qty',
        'date',
        'pid',
    ];
    
}