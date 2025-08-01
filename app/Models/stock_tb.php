<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\vendor_tb as Vendor;
use App\Models\stock_allocation as StockAllocation;

class stock_tb extends Model
{
    //
    protected $table = "stock_tb";
    protected $primaryKey = "stkid";
    public $timestamps = false;
    protected $fillable = [
        'customer', 'part_no', 'rev', 'supplier', 'dc', 'finish',
        'docsready', 'dtadded', 'manuf_dt', 'uprice', 'qty', 'comments'
    ];
    public function customer()
    {
        return $this->belongsTo(data_tb::class, 'customer', 'data_id');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'supplier', 'data_id');
    }
    public function allocations()
    {
        return $this->hasMany(StockAllocation::class, 'stock_id')
            ->where('delivered_on', '00-00-0000');
    }
}