<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\data_tb as Customer;

class maincont_tb extends Model
{
    //
    protected $table = 'maincont_tb';
    protected $primaryKey = 'enggcont_id';
    public $timestamps = false;
        public function customer()
    {
        return $this->belongsTo(Customer::class, 'coustid', 'data_id');
    }
}
