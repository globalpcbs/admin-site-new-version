<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class alerts_tb extends Model
{
    //
    protected $table = 'alerts_tb';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'customer',
        'part_no',
        'rev',
        'alert',
        'viewable',
        'atype',
    ];
}