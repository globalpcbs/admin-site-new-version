<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\invoice_items_tb as InvoiceItem;

class invoice_tb extends Model
{
    //
    protected $table = 'invoice_tb';
    protected $primaryKey = 'invoice_id';
    public $timestamps = false;
    protected $fillable = [
        'vid',
        'sid',
        'namereq',
        'svia',
        'svia_oth',
        'fcharge',
        'city',
        'state',
        'sterm',
        'comments',
        'podate',
        'customer',
        'part_no',
        'rev',
        'delto',
        'ord_by',
        'date1',
        'po',
        'our_ord_num',
        'saletax',
        'no_layer',
        'comval',
    ];
    
    // for items of invoice ..
    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'pid', 'invoice_id');
    }
    // customer ..
    public function custo(){
        return $this->belongsTo(data_tb::class,'vid');
    }
}