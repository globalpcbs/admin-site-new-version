<?php

namespace App\Livewire\Misc;

use Livewire\Component;
use App\Models\stock_tb;
use App\Models\vendor_tb as Supplier;
use App\Models\order_tb  as Order;

class AddStock extends Component
{
    public $customer, $part_no, $rev, $supplier, $dc, $finish;
    public $docsready = 'N';
    public $date_added, $manufacturing_date;
    public $uprice, $qty, $comments;
    public $search = '';
    public $matches = [];
    public $inputKey;

    public function mount()
    {
        $this->inputKey = uniqid();
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
            'dtadded' => $this->date_added,
            'manuf_dt' => $this->manufacturing_date,
            'uprice' => $this->uprice,
            'qty' => $this->qty,
            'comments' => $this->comments,
        ]);

        session()->flash('success', 'Stock record added successfully.');
        return redirect(route('misc.manage-stock'));
        // Optional: reset form
        // $this->reset([
        //     'customer', 'part_no', 'rev', 'supplier', 'dc', 'finish',
        //     'docsready', 'date_added', 'manufacturing_date',
        //     'uprice', 'qty', 'comments'
        // ]);

    }

    public function render()
    {
        $suppliers = Supplier::orderBy('c_name')->get();
        return view('livewire.misc.add-stock', compact('suppliers'))
            ->layout('layouts.app', ['title' => 'Add Stock']);
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
            ->get()
            ->map(fn ($row) => [
                'label' => "{$row->part_no}_{$row->rev}_{$row->cust_name}",
                'part' => $row->part_no,
                'rev' => $row->rev,
                'cust' => $row->cust_name,
            ])
            ->toArray();
    }

    public function useMatch(int $i)
    {
        if (!isset($this->matches[$i])) return;

        $m = $this->matches[$i];
        $this->selectLookup($m['part'], $m['rev'], $m['cust']);
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
}