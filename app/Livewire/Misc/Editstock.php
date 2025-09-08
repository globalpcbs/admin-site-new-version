<?php

namespace App\Livewire\Misc;

use Livewire\Component;
use App\Models\stock_tb;
use App\Models\vendor_tb as Supplier;
use App\Models\data_tb;
use App\Models\order_tb  as Order;
use Carbon\Carbon;
use App\Models\stock_allocation as StockAllocation;

class Editstock extends Component
{
    public $stockId;

    public $customer, $part_no, $rev, $supplier, $dc, $finish;
    public $docsready = 'N';
    public $date_added, $manufacturing_date;
    public $uprice, $qty, $comments;
    public $shelf_life, $panel = false;
    public $showModal = false;
    public $allocId, $stockid;
    public $alloc_customer, $alloc_pono, $alloc_duedate, $alloc_allocationdate;
    public $alloc_qut, $alloc_allocate_by, $alloc_deliveredon;
    public $modal_duedate;
    public $modal_customer;
    public $modal_pono;
    public $modal_allocationdate;
    public $modal_qut;
    public $modal_allocate_by;
    public $modal_deliveredon;
    
    public $search = '';
    public $matches = [];
    public $inputKey;
    public $customer_search = [];

    // Helper to clean and format date
private function cleanDate(?string $dateString): ?string
{
    if (!$dateString) return null;

    // Try splitting out weekday if it's present
    $parts = explode('-', $dateString);
    if (count($parts) === 4) {
        array_shift($parts); // Remove weekday
        $clean = implode('-', $parts); // e.g., 12-26-2018
        return Carbon::createFromFormat('m-d-Y', $clean)->format('Y-m-d');
    }

    // Fallback if format is standard
    try {
        return Carbon::parse($dateString)->toDateString();
    } catch (\Exception $e) {
        return null; // Or log error if needed
    }
}
    public function mount($id)
    {
        $this->stockId = $id;
        $stock = stock_tb::findOrFail($id);

        $this->customer = $stock->customer;
        $this->part_no = $stock->part_no;
        $this->rev = $stock->rev;
        $this->supplier = $stock->supplier;
        $this->dc = $stock->dc;
        $this->finish = $stock->finish;
        $this->docsready = $stock->docsready ?? 'N';
        // $this->date_added = $stock->dtadded;
        // $this->manufacturing_date = $stock->manuf_dt;
        $this->date_added = $this->cleanDate($stock->dtadded);
        $this->manufacturing_date = $this->cleanDate($stock->manuf_dt);
        $this->uprice = $stock->uprice;
        $this->qty = $stock->qty;
        $this->comments = $stock->comments;
        $this->shelf_life = $stock->shelf_life;
        $this->panel = $stock->panel === 'Y' ? true : false;
        $this->inputKey = uniqid();
    }
       public function getTotalProperty()
    {
        return floatval($this->uprice) * intval($this->qty);
    }
    public function update()
    {
        $this->validate([
            'customer' => 'required|string',
            'part_no' => 'required',
            'rev' => 'nullable',
        ]);

        $stock = stock_tb::findOrFail($this->stockId);
        $stock->update([
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
            'shelf_life' => $this->shelf_life,
            'panel' => $this->panel ? 'Y' : 'N',
        ]);

        session()->flash('success', 'Stock updated successfully.');
        return redirect()->route('misc.manage-stock');
    }
   public function openModal()
{
//    $this->resetInput();
    $this->showModal = true;
}

public function edit($id)
{
    $al = StockAllocation::findOrFail($id);

    $this->allocId = $id;
    $this->alloc_customer = $al->customer;
    $this->alloc_pono = $al->pono;
    $this->alloc_duedate = $al->due_date;
    $this->alloc_allocationdate = $al->allocationdate;
    $this->alloc_qut = $al->qut;
    $this->alloc_allocate_by = $al->allocate_by_id;
    $this->alloc_deliveredon = $al->delivered_on !== '0000-00-00' ? $al->delivered_on : null;
    $this->showModal = true;
}

    public function save()
    {
        $duedate = $this->formatDate($this->alloc_duedate);
        $allocationdate = $this->formatDate($this->alloc_allocationdate);
        $deliveredon = $this->formatDate($this->alloc_deliveredon);

        $al = $this->allocId ? StockAllocation::find($this->allocId) : new StockAllocation();
        $al->stock_id = $this->stockId;
        $al->customer = $this->alloc_customer;
        $al->pono = $this->alloc_pono;
        $al->due_date = $duedate;
        $al->allocationdate = $allocationdate;
        $al->qut = $this->alloc_qut;
        $al->allocate_by_id = $this->alloc_allocate_by;
        $al->delivered_on = $deliveredon ?? null;
        $al->save();

        // Update stock quantity only if delivered
        if ($deliveredon) {
            $stock = stock_tb::where('stkid', $this->stockId)->first();
            $stock->qty -= $this->alloc_qut;
            $stock->save();
            $this->qty = $stock->qty;
            $this->inputKey = uniqid();
        }
       // dd($this->qty);
        $this->showModal = false;
        $this->resetInput();
    }

public function delete($id)
{
    StockAllocation::findOrFail($id)->delete();
}

private function resetInput()
{
    $this->allocId = null;
    $this->modal_customer = '';
$this->modal_pono = '';
$this->modal_duedate = '';
$this->modal_allocationdate = '';
$this->modal_qut = '';
$this->modal_allocate_by = '';
$this->modal_deliveredon = '';
}

private function formatDate($value)
{
    if (!$value) return null;
    return Carbon::parse($value)->format('Y-m-d');
}

    public function render()
    {
        $suppliers = Supplier::orderBy('c_name')->get();
         $pending = StockAllocation::where('stock_id', $this->stockId)
        ->where('delivered_on', '0000-00-00')->orWhere('delivered_on', null)
        ->get();
//        dd($this->stockId);
        $delivered = StockAllocation::where('stock_id', $this->stockId)
            ->where('delivered_on', '!=', '0000-00-00')
            ->get();
      //  dd($delivered->count());
        return view('livewire.misc.editstock', compact('suppliers','pending', 'delivered'))
            ->layout('layouts.app', ['title' => 'Edit Stock']);
    }
    // for search customer ...
    public function onKeyUpForCustomer(string $value){
        if (mb_strlen(trim($value)) < 2) {
            $this->customer_search = [];
            return;
        }
        $this->customer_search  = data_tb::query()
        ->select('c_shortname')
        ->where('c_name','like',"%{$value}%")
        ->orWhere('c_shortname','like',"%{$value}%")
        ->get()
        ->toArray();
       // dd($this->customer_search);
    }
    public function useCustomerMatch(int $i){
        $customer = $this->customer_search[$i]['c_shortname'];
        $this->alloc_customer = $customer;
        $this->customer_search = [];
        $this->inputKey = uniqid();
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