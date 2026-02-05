<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Models\porder_tb;
use App\Models\data_tb;
use App\Models\vendor_tb;

class StatusReport extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $showModal = false;
    public $orderId;
    public $cus_due;
    public $sup_due;
    public $selectedPoid;
    
    public $showNoteModal = false;
    public $note = '';
    public $poidForNote;
    
    // Filter properties - Bound to UI inputs
    public $from = '';
    public $to = '';
    public $partNumber = '';
    public $customerName = '';
    public $vendorName = '';

    // Active Search Filters - Used for querying
    public $activeFrom = '';
    public $activeTo = '';
    public $activePartNumber = '';
    public $activeCustomerName = '';
    public $activeVendorName = '';
    
    // Auto-complete suggestions
    public $partNumberSuggestions = [];
    public $customerNameSuggestions = [];
    public $vendorNameSuggestions = [];

    public function openNoteModal($poid)
    {
        $this->poidForNote = $poid;
        $order = porder_tb::find($poid);
        $this->note = $order?->note ?? '';
        $this->showNoteModal = true;
    }

    public function saveNote()
    {
        $order = porder_tb::find($this->poidForNote);
        if ($order) {
            $order->note = $this->note;
            $order->save();
        }
        $this->showNoteModal = false;
        // No need to loadAllData(), render() will handle it
        session()->flash('success', 'Note updated successfully.');
    }

    public function openModal($id)
    {
        $order = porder_tb::findOrFail($id);
        $this->selectedPoid = $order->poid;
        $this->orderId = $order->poid;
        $this->cus_due = $order->cus_due;
        $this->sup_due = $order->supli_due;
        $this->showModal = true;
    }

    public function updateDueDates()
    {
        $order = porder_tb::findOrFail($this->orderId);
        $order->cus_due = $this->cus_due;
        $order->supli_due = $this->sup_due;
        $order->save();

        $this->reset(['showModal', 'orderId', 'cus_due', 'sup_due']);
        session()->flash('success', 'Due dates updated successfully!');
    }
    
    public function closeModal()
    {
        $this->showModal = false;
    }
    
    public function mount()
    {
        // Initial load is handled by render()
    }

    // Auto-complete methods
    public function updatedPartNumber($value)
    {
        if (strlen($value) >= 2) {
            $this->partNumberSuggestions = porder_tb::where('part_no', 'like', '%' . $value . '%')
                ->distinct()
                ->orderBy('part_no')
                ->take(10)
                ->pluck('part_no')
                ->toArray();
        } else {
            $this->partNumberSuggestions = [];
        }
    }

    public function updatedCustomerName($value)
    {
        if (strlen($value) >= 2) {
            $this->customerNameSuggestions = data_tb::where('c_name', 'like', '%' . $value . '%')
                ->orWhere('c_shortname', 'like', '%' . $value . '%')
                ->distinct()
                ->orderBy('c_name')
                ->take(10)
                ->pluck('c_name')
                ->toArray();
        } else {
            $this->customerNameSuggestions = [];
        }
    }

    public function updatedVendorName($value)
    {
        if (strlen($value) >= 2) {
            $this->vendorNameSuggestions = vendor_tb::where('c_shortname', 'like', '%' . $value . '%')
                ->orWhere('c_name', 'like', '%' . $value . '%')
                ->distinct()
                ->orderBy('c_shortname')
                ->take(10)
                ->pluck('c_shortname')
                ->toArray();
        } else {
            $this->vendorNameSuggestions = [];
        }
    }

    // Key to force re-render of inputs
    public $refreshKey = 0;

    // Search method
    public function search()
    {
        // Copy UI inputs to Active Search filters
        $this->activeFrom = $this->from;
        $this->activeTo = $this->to;
        $this->activePartNumber = $this->partNumber;
        $this->activeCustomerName = $this->customerName;
        $this->activeVendorName = $this->vendorName;

        $this->resetPage();
        
        // Clear UI inputs as requested
        $this->from = '';
        $this->to = '';
        $this->partNumber = '';
        $this->customerName = '';
        $this->vendorName = '';
        
        // Clear suggestions
        $this->partNumberSuggestions = [];
        $this->customerNameSuggestions = [];
        $this->vendorNameSuggestions = [];
        
        $this->refreshKey++; // Force inputs to re-render
        $this->dispatch('clear-inputs');
    }

    // Reset filters
    public function resetFilters()
    {
        // Clear Active filters
        $this->activeFrom = '';
        $this->activeTo = '';
        $this->activePartNumber = '';
        $this->activeCustomerName = '';
        $this->activeVendorName = '';

        // Clear UI inputs
        $this->from = '';
        $this->to = ''; 
        $this->partNumber = ''; 
        $this->customerName = '';
        $this->vendorName = '';
        
        // Clear suggestions
        $this->partNumberSuggestions = [];
        $this->customerNameSuggestions = [];
        $this->vendorNameSuggestions = [];

        $this->refreshKey++; // Force inputs to re-render
        $this->resetPage();
        $this->dispatch('clear-inputs');
        
        session()->flash('success', 'Filters reset successfully.');
    }

    public function toggleOrder($poid, $isChecked)
    {
        DB::table('porder_tb')
            ->where('poid', $poid)
            ->update(['allow' => $isChecked ? 'true' : 'false']);
            
        session()->flash('success', 'WT Status Has Been updated successfully!');
    }
    
    public function render()
    {
        $query = DB::table('porder_tb as p')
            ->selectRaw("p.*, i.invoice_id, i.podate as invoicedon, v.c_shortname as vc, UNIX_TIMESTAMP(STR_TO_DATE(p.dweek,'%m-%d-%Y')) as dw")
            ->leftJoin('invoice_tb as i', function($join) {
                // Corresponding logic to: ON (p.part_no = i.part_no AND p.rev = i.rev AND p.po = i.po)
                $join->on('p.part_no', '=', 'i.part_no')
                     ->on('p.rev', '=', 'i.rev')
                     ->on('p.po', '=', 'i.po');
            })
            ->leftJoin('vendor_tb as v', 'v.data_id', '=', 'p.vid');

        // Apply Active Filters
        if ($this->activeFrom && $this->activeTo) {
             $query->whereRaw("STR_TO_DATE(p.dweek, '%m-%d-%Y') BETWEEN STR_TO_DATE(?, '%Y-%m-%d') AND STR_TO_DATE(?, '%Y-%m-%d')", [$this->activeFrom, $this->activeTo]);
        }

        if ($this->activePartNumber) {
            $query->where('p.part_no', 'like', '%' . $this->activePartNumber . '%');
        }

        if ($this->activeCustomerName) {
            $query->where('p.customer', 'like', '%' . $this->activeCustomerName . '%');
        }

        if ($this->activeVendorName) {
            $query->where('v.c_shortname', 'like', '%' . $this->activeVendorName . '%');
        }

        $orders = $query->orderBy('p.poid', 'asc')->paginate(100);

        return view('livewire.reports.status-report', [
            'orders' => $orders
        ])->layout('layouts.app', ['title' => 'Reports']);
    }
}