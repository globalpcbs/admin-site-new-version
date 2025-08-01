<?php

namespace App\Livewire\Misc;

use Livewire\Component;
use App\Models\packing_tb_loged;
use App\Models\data_tb;
use App\Models\vendor_tb;
use App\Models\porder_tb as Order;
use Carbon\Carbon;

class Editlogged extends Component
{
    public $log_id;

    public $part_no11, $customer, $part_no, $supplier_id, $rev, $rec_on, $otd = 'Yes',
           $customer_po, $cus_due_date, $qty_ordered, $qty_rec, $qty_due,
           $shipped_on, $qty_insp, $qty_passed, $inspected_by, $solder_sample,
           $ncr, $comment, $qty_shipped;

    public $matches = [];
    public $customers = [];
    public $shippers = [];

    public function mount($log_id)
    {
        $this->log_id = $log_id;
        $log = packing_tb_loged::findOrFail($log_id);

        $this->part_no11     = $log->our_po;
        $this->customer      = $log->customer;
        $this->part_no       = $log->part_no;
        $this->supplier_id   = $log->supplier;
        $this->rev           = $log->rev;
        $this->rec_on        = $log->rec_on;
        $this->otd           = $log->otd;
        $this->customer_po   = $log->customer_po;
        $this->cus_due_date  = $log->cus_due_date;
        $this->qty_ordered   = $log->qty_ordered;
        $this->qty_rec       = $log->qty_rec;
        $this->qty_due       = $log->qty_due;
        $this->shipped_on    = $log->shipped_on;
        $this->qty_insp      = $log->qty_insp;
        $this->qty_passed    = $log->qty_passed;
        $this->inspected_by  = $log->inspected_by;
        $this->solder_sample = $log->solder_sample;
        $this->ncr           = $log->ncr;
        $this->comment       = $log->comment;
        $this->qty_shipped   = $log->qty_shipped;

        $this->customers = data_tb::orderBy('c_name')->get();
        $this->shippers = vendor_tb::orderBy('c_name')->get();
    }

    public function update()
    {
        $log = packing_tb_loged::findOrFail($this->log_id);

        $log->our_po         = $this->part_no11;
        $log->customer       = $this->customer;
        $log->part_no        = $this->part_no;
        $log->supplier       = $this->supplier_id;
        $log->rev            = $this->rev;
        $log->rec_on         = $this->rec_on;
        $log->otd            = $this->otd;
        $log->customer_po    = $this->customer_po;
        $log->cus_due_date   = $this->formatDate($this->cus_due_date);
        $log->qty_ordered    = $this->qty_ordered;
        $log->qty_rec        = $this->qty_rec;
        $log->qty_due        = $this->qty_due;
        $log->shipped_on     = $this->formatDate($this->shipped_on);
        $log->qty_insp       = $this->qty_insp;
        $log->qty_passed     = $this->qty_passed;
        $log->inspected_by   = $this->inspected_by;
        $log->solder_sample  = $this->solder_sample;
        $log->ncr            = $this->ncr;
        $log->comment        = $this->comment;
        $log->qty_shipped    = $this->qty_shipped;
        $log->save();

        session()->flash('success', 'Packing slip updated successfully!');
        return redirect(route('misc.receiving-log')); // or your manage page
    }

    protected function formatDate($date)
    {
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function onKeyUp(string $value): void
    {
        $this->part_no11 = $value;

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

    public function useMatch(string $po_number): void
    {
        $this->matches = [];

        $porder = Order::where('po_number', $po_number)->first();

        if (!$porder) {
            return;
        }

        $this->part_no11   = $po_number;
        $this->part_no     = $porder->part_no;
        $this->customer    = $porder->customer;
        $this->customer_po = $porder->po;
        $this->rev         = $porder->rev;
    }

    public function render()
    {
        return view('livewire.misc.editlogged')->layout('layouts.app', ['title' => 'Edit Logged Slip']);
    }
}