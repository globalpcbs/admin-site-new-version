<?php
namespace App\Livewire\PackingSlips;

use Livewire\Component;
use App\Models\packing_tb;
use App\Models\packing_tb_loged;
use App\Models\data_tb;
use App\Models\vendor_tb;
use App\Models\porder_tb as Order;
use Carbon\Carbon;

class Loged extends Component
{
    public $invoice_id;
    public $part_no11, $customer, $part_no, $supplier, $rev, $rec_on, $otd = 'Yes',
           $customer_po, $cus_due_date, $qty_ordered, $qty_rec, $qty_due,
           $shipped_on, $qty_insp, $qty_passed, $inspected_by, $solder_sample,
           $ncr, $comment, $qty_shipped;
    public $supplier_id;
    public $shippers;
    public $customers = [];
    public $matches = [];
    public $packing;
    public $search;

    public function mount($invoice_id)
    {
        $this->invoice_id = $invoice_id;
        $this->customers = data_tb::orderBy('c_name')->get();

        $this->packing = packing_tb::findOrFail($invoice_id);
        $this->part_no = $this->packing->part_no;
        $this->customer_po = $this->packing->po;
        $this->customer = $this->packing->customer;
        $this->shippers = vendor_tb::orderby('c_name','ASC')->get();
       // dd($this->packing);
    }

    public function save()
    {
        dd($this->supplier_id);
        $log = new packing_tb_loged();
        $log->invoice_id = $this->invoice_id;
        $log->our_po = $this->part_no11;
        $log->customer = $this->customer;
        $log->part_no = $this->part_no;
        $log->supplier = $this->supplier_id;
        $log->rev = $this->rev;
        $log->rec_on = $this->rec_on;
        $log->otd = $this->otd;
        $log->customer_po = $this->customer_po;
        $log->cus_due_date = $this->formatDate($this->cus_due_date);
        $log->qty_ordered = $this->qty_ordered;
        $log->qty_rec = $this->qty_rec;
        $log->qty_due = $this->qty_due;
        $log->shipped_on = $this->formatDate($this->shipped_on);
        $log->qty_insp = $this->qty_insp;
        $log->qty_passed = $this->qty_passed;
        $log->inspected_by = $this->inspected_by;
        $log->solder_sample = $this->solder_sample;
        $log->ncr = $this->ncr;
        $log->comment = $this->comment;
        $log->qty_shipped = $this->qty_shipped;
        $log->save();

        $this->packing->loged = 1;
        $this->packing->save();

        session()->flash('success', 'Packing slip logged successfully!');
        return redirect()->route('packing.manage');
    }

    protected function formatDate($date)
    {
        if (!$date) return null;
        try {
            return Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function render()
    {
        return view('livewire.packing-slips.loged')->layout('layouts.app', ['title' => 'In Logged Slip']);
    }
    public function onKeyUp(string $value): void
    {
        $this->part_no11 = $value; // keep input synced (if needed)
    
        if (mb_strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }
    
        $this->matches = Order::query()
            ->select('po_number')
            ->where('po_number', 'like', '%' . $value . '%')
            ->distinct()
            ->get()
            ->map(fn ($row) => [
                'label' => $row->po_number,
                'value' => $row->po_number,
            ])->toArray();
    }

     /** Handle the click coming from <li wire:click="useMatch($i)"> */
     public function useMatch(string $po_number): void
     {
         $this->matches = [];
     
         $porder = Order::where('po_number', $po_number)->first();
     
         if (!$porder) {
             return;
         }
         $this->part_no11 = $po_number;         // update the visible input field
         $this->part_no = $porder->part_no;     // update part_no
         $this->customer = $porder->customer;
         $this->customer_po = $porder->po;
         $this->rev = $porder->rev;
         $this->supplier = vendor_tb::where('data_id',$porder->vid)->first();
         $this->dispatch('$refresh'); // Force re-render
     }
     
}