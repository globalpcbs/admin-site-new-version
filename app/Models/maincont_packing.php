<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class maincont_packing extends Model
{
    // 
    protected $table = 'maincont_packing';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'maincontid',
        'packingid',
    ];
}