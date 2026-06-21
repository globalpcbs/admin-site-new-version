<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vendor_maincont_tb extends Model
{
    //
    protected $table = "vendor_maincont_tb";
    public $timestamps = false;
    protected $primaryKey = 'enggcont_id';
    protected $fillable = [
        'coustid', // 👈 Add this line
    ];
    
    public function vendor(){
        // If vendor_maincont_tb has 'custid' and vendor_tb has 'data_id' as primary key
        return $this->belongsTo(vendor_tb::class, 'custid', 'data_id');
    }
}