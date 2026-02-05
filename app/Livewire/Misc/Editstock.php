<?php

namespace App\Livewire\Misc;

use Livewire\Component;
use App\Models\stock_tb;
use App\Models\vendor_tb as Supplier;
use App\Models\data_tb;
use App\Models\order_tb as Order;
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

        // ✅ Parse dates safely with multiple format support
        $this->date_added = $this->parseDateSafely($stock->dtadded);
        $this->manufacturing_date = $this->parseDateSafely($stock->manuf_dt);

        $this->uprice = $stock->uprice;
        $this->qty = $stock->qty;
        $this->comments = $stock->comments;
        $this->shelf_life = $stock->shelflife;
        $this->panel = $stock->panel === 'Y';

        $this->inputKey = uniqid();

        // ✅ Dispatch AFTER values exist
        if ($this->date_added) {
            $this->dispatch('setDateAdded', $this->date_added);
        }

        if ($this->manufacturing_date) {
            $this->dispatch('setManufDate', $this->manufacturing_date);
        }
    }

    /**
     * Safely parse dates from various formats to m-d-Y
     */
    private function parseDateSafely($dateString)
    {
        if (empty($dateString) || $dateString === '0000-00-00') {
            return null;
        }

        // If already in m-d-Y format, return as is
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $dateString)) {
            try {
                // Validate it's a real date
                Carbon::createFromFormat('m-d-Y', $dateString);
                return $dateString;
            } catch (\Exception $e) {
                // Continue to try other formats
            }
        }

        // Try different date formats
        $formats = [
            'Y-m-d',    // 2024-12-31 (MySQL standard)
            'd-m-Y',    // 31-12-2024
            'm/d/Y',    // 12/31/2024
            'd/m/Y',    // 31/12/2024
            'Y/m/d',    // 2024/12/31
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString);
                return $date->format('m-d-Y');
            } catch (\Exception $e) {
                continue;
            }
        }

        // Last resort: let Carbon try to guess
        try {
            return Carbon::parse($dateString)->format('m-d-Y');
        } catch (\Exception $e) {
            \Log::warning("Failed to parse date: {$dateString}", ['error' => $e->getMessage()]);
            return null;
        }
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
            'manufacturing_date' => 'nullable|date_format:m-d-Y',
            'date_added' => 'nullable|date_format:m-d-Y',
        ]);

        $stock = stock_tb::findOrFail($this->stockId);
        $stock->customer = $this->customer;
        $stock->part_no = $this->part_no;
        $stock->rev = $this->rev;
        $stock->supplier = $this->supplier;
        $stock->dc = $this->dc;
        $stock->finish = $this->finish;
        $stock->docsready = $this->docsready;
        
        // Store dates as-is (already validated as m-d-Y)
        $stock->dtadded = $this->date_added;
        $stock->manuf_dt = $this->manufacturing_date;
        
        $stock->uprice = $this->uprice;
        $stock->qty = $this->qty;
        $stock->comments = $this->comments;
        $stock->shelflife = $this->shelf_life;
       // $stock->panel = $this->panel ? 'Y' : 'N';

        $stock->save();

        session()->flash('success', 'Stock updated successfully.');
        return redirect()->route('misc.manage-stock');
    }

    public function openModal()
    {
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
        $this->alloc_customer = '';
        $this->alloc_pono = '';
        $this->alloc_duedate = '';
        $this->alloc_allocationdate = '';
        $this->alloc_qut = '';
        $this->alloc_allocate_by = '';
        $this->alloc_deliveredon = '';
        
        // Also reset modal variables
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
        
        try {
            // Try to parse from m-d-Y format first (from flatpickr)
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                return Carbon::createFromFormat('m-d-Y', $value)->format('Y-m-d');
            }
            
            // Otherwise, try standard parsing
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            \Log::warning("Failed to format date: {$value}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function render()
    {
        $suppliers = Supplier::orderBy('c_name')->get();
        
        $pending = StockAllocation::where('stock_id', $this->stockId)
            ->where(function($query) {
                $query->where('delivered_on', '0000-00-00')
                      ->orWhereNull('delivered_on');
            })
            ->get();

        $delivered = StockAllocation::where('stock_id', $this->stockId)
            ->where('delivered_on', '!=', '0000-00-00')
            ->whereNotNull('delivered_on')
            ->get();

        return view('livewire.misc.editstock', compact('suppliers', 'pending', 'delivered'))
            ->layout('layouts.app', ['title' => 'Edit Stock']);
    }

    // for search customer ...
    public function onKeyUpForCustomer(string $value)
    {
        if (mb_strlen(trim($value)) < 2) {
            $this->customer_search = [];
            return;
        }
        
        $this->customer_search = data_tb::query()
            ->select('c_shortname')
            ->where('c_name', 'like', "%{$value}%")
            ->orWhere('c_shortname', 'like', "%{$value}%")
            ->get()
            ->toArray();
    }

    public function useCustomerMatch(int $i)
    {
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