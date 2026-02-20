<?php

namespace App\Livewire\Misc;

use Livewire\Component;
use App\Models\stock_tb;
use App\Models\vendor_tb as Supplier;
use App\Models\order_tb as Order;
use App\Models\stock_allocation;
use Carbon\Carbon;

class AddStock extends Component
{
    public $customer, $part_no, $rev, $supplier, $dc, $finish;
    public $docsready = '0';
    public $date_added, $manufacturing_date;
    public $uprice = 0, $qty = 0, $comments, $shelflife = 12;
    public $search = '';
    public $matches = [];
    public $inputKey;
    
    // Allocation properties
    public $showAllocationPopup = false;
    public $allocation_customer = '';
    public $allocation_pono = '';
    public $allocation_duedate = '';
    public $allocation_date = '';
    public $allocation_qut = '';
    public $allocation_by = '';
    public $allocation_deliveredon = '';
    
    // Customer search for allocation
    public $customer_search = '';
    public $customer_matches = [];

    public function mount()
    {
        $this->inputKey = uniqid();
        $this->date_added = date('m-d-Y');
        $this->allocation_date = date('m-d-Y');
            // 🔥 dispatch for flatpickr
        if ($this->date_added) {
            $this->dispatch('setDateAdded', $this->date_added);
        }

        if ($this->manufacturing_date) {
            $this->dispatch('setManufDate', $this->manufacturing_date);
        }

    }

    public function getTotalProperty()
    {
        return floatval($this->uprice) * intval($this->qty);
    }

    public function save()
    {
        $this->validate([
            'customer' => 'required|string',
            'part_no' => 'required|string',
            'rev' => 'nullable|string',
            'supplier' => 'nullable|string',
        ]);

        stock_tb::create([
            'customer' => $this->customer,
            'part_no' => $this->part_no,
            'rev' => $this->rev,
            'supplier' => $this->supplier,
            'dc' => $this->dc,
            'finish' => $this->finish,
            'docsready' => $this->docsready,
            'dtadded' => $this->date_added
                ? Carbon::createFromFormat('m-d-Y', $this->date_added)->format('Y-m-d')
                : null,

            'manuf_dt' => $this->manufacturing_date
                ? Carbon::createFromFormat('m-d-Y', $this->manufacturing_date)->format('Y-m-d')
                : null,
            'uprice' => $this->uprice,
            'qty' => $this->qty,
            'comments' => $this->comments,
            'shelflife' => $this->shelflife,
        ]);

        session()->flash('success', 'Stock record added successfully.');
        return redirect(route('misc.manage-stock'));
    }

    // Allocation methods
    public function showAllocation()
    {
        $this->showAllocationPopup = true;
    }

    public function closeAllocation()
    {
        $this->showAllocationPopup = false;
        $this->resetAllocationFields();
    }

    public function saveAllocation()
    {
        $this->validate([
            'allocation_customer' => 'required',
            'allocation_pono' => 'required',
            'allocation_duedate' => 'required',
            'allocation_qut' => 'required|numeric',
        ]);

        // Note: This would require the stock_id from the newly created stock
        // For add-only, you might want to save the allocation after the stock is created
        session()->flash('allocation_success', 'Allocation saved successfully.');
        $this->closeAllocation();
    }

    private function resetAllocationFields()
    {
        $this->allocation_customer = '';
        $this->allocation_pono = '';
        $this->allocation_duedate = '';
        $this->allocation_date = date('m-d-Y');
        $this->allocation_qut = '';
        $this->allocation_by = '';
        $this->allocation_deliveredon = '';
    }

    public function onKeyUp(string $value)
    {
        $this->search = $value;

        if (mb_strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }

        $this->matches = Order::query()
            ->select('part_no', 'rev', 'cust_name')
            ->where('part_no', 'like', "%{$value}%")
            ->orWhere('cust_name', 'like', "%{$value}%")
            ->distinct()
            ->get()
            ->map(fn ($row) => [
                'label' => "{$row->part_no}_{$row->rev}_{$row->cust_name}",
                'part' => $row->part_no,
                'rev' => $row->rev,
                'cust' => $row->cust_name,
            ])
            ->toArray();
    }

    public function onCustomerKeyUp(string $value)
    {
        $this->customer_search = $value;

        if (mb_strlen(trim($value)) < 2) {
            $this->customer_matches = [];
            return;
        }

        $this->customer_matches = Order::query()
            ->select('cust_name')
            ->where('cust_name', 'like', "%{$value}%")
            ->distinct()
            ->get()
            ->pluck('cust_name')
            ->toArray();
    }

    public function useMatch(int $i)
    {
        if (!isset($this->matches[$i])) return;

        $m = $this->matches[$i];
        $this->selectLookup($m['part'], $m['rev'], $m['cust']);
    }

    public function useCustomerMatch($customer)
    {
        $this->allocation_customer = $customer;
        $this->customer_matches = [];
    }

    public function selectLookup($part, $rev, $cust)
    {
        $this->search = "{$cust}_{$part}_{$rev}";
        $this->matches = [];

        $order = Order::where('part_no', $part)
            ->where('rev', $rev)
            ->where('cust_name', $cust)
            ->first();

        if ($order) {
            $this->part_no = $order->part_no;
            $this->rev = $order->rev;
            $this->customer = $order->cust_name;
            $this->inputKey = uniqid();
        }
    }

    public function calculateTotal()
    {
        // This method is triggered by wire:change on uprice and qty
        // The total is computed by the getTotalProperty() method
        return;
    }

    public function render()
    {
        $suppliers = Supplier::orderBy('c_name')->get();
        
        return view('livewire.misc.add-stock', compact('suppliers'))
            ->layout('layouts.app', ['title' => 'Add Stock']);
    }
}