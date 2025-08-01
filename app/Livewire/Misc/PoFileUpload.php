<?php 
namespace App\Livewire\Misc;

use Livewire\Component;
use App\Models\order_tb;

class PoFileUpload extends Component
{
    public $records = [];

    public function mount()
    {
        $this->records = order_tb::selectRaw('MIN(ord_id) as id, cust_name, part_no, rev')
            ->groupBy('part_no', 'rev', 'cust_name')
            ->orderBy('cust_name')
            ->get();
    }

    public function render()
    {
        return view('livewire.misc.po-file-upload')->layout('layouts.app', ['title' => 'PO File Upload']);
    }
}