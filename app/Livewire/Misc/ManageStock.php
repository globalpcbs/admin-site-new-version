<?php

namespace App\Livewire\Misc;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\stock_tb;
use App\Models\data_tb;
use Illuminate\Support\Facades\DB;

class ManageStock extends Component
{
    use WithPagination;

    public $searchPartNoInput = '';
    public $searchCustomerInput = '';
    public $searchPartNo = '';
    public $searchCustomer = '';
    
    public $confirmingDelete = null;
    protected $paginationTheme = 'bootstrap';

    public $alertMessage = '';
    public $alertType = '';
    protected $listeners = ['alert-hidden' => 'clearAlert'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function updatingSearchPartNo() { 
        $this->resetPage(); 
    }
    
    public function updatingSearchCustomer() { 
        $this->resetPage(); 
    }

    public function searchq()
    {
        // Reset customer filter
        $this->reset(['searchCustomer', 'searchCustomerInput']);
        // Apply part number filter
        $this->searchPartNo = trim($this->searchPartNoInput);
        // Clear input property
        $this->searchPartNoInput = '';
        $this->resetPage();
    }

    public function searchbyCustomer()
    {
        // Reset part number filter
        $this->reset(['searchPartNo', 'searchPartNoInput']);
        // Apply customer filter
        $this->searchCustomer = trim($this->searchCustomerInput);
        // Clear input property
        $this->searchCustomerInput = '';
        $this->resetPage();
    }

    public function filterclose()
    {
        // Reset all search properties
        $this->reset([
            'searchPartNoInput',
            'searchCustomerInput',
            'searchPartNo',
            'searchCustomer'
        ]);
        $this->resetPage();

        // Optional: dispatch a browser event (used as a backup)
        $this->dispatch('reset-filters-complete');
    }

    public function delete($id)
    {
        stock_tb::where('stkid', $id)->delete();
        DB::table('stock_ret')->where('stkid', $id)->delete();
        
        $this->alertMessage = 'Stock deleted successfully.';
        $this->alertType = 'danger';
        
        $this->dispatch('refresh-component');
    }

    public function duplicate($id)
    {
        $stock = stock_tb::findOrFail($id)->replicate();
        $stock->save();
        
        $this->alertMessage = 'Stock duplicated successfully.';
        $this->alertType = 'success';
        
        $this->dispatch('refresh-component');
    }

    public function render()
    {
        $stocks = stock_tb::query()
            ->when($this->searchPartNo, fn($q) => $q->where('part_no', 'like', '%' . $this->searchPartNo . '%'))
            ->when($this->searchCustomer, fn($q) => $q->where('customer', 'like', '%' . $this->searchCustomer . '%'))
            ->with(['vendor', 'allocations'])
            ->orderBy('stkid','asc')
            ->paginate(100);

        return view('livewire.misc.manage-stock', compact('stocks'))
            ->layout('layouts.app', ['title' => 'Manage Stock']);
    }
}