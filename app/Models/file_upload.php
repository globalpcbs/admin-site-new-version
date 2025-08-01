<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class file_upload extends Model
{
    //
    protected $table = "file_upload";
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'customer',
        'part_no',
        'rev',
        'name',
        'path',
        'date',
    ];
}