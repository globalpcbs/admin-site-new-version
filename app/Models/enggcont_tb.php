<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\data_tb as Customer;

class enggcont_tb extends Model
{
    //
    protected $table = 'enggcont_tb';
    protected $primaryKey = 'enggcont_id';
    public $timestamps = false;

    protected $fillable = [
        'name', 'lastname', 'phone', 'email', 'mobile', 'coustid',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'coustid', 'data_id');
    }
}
