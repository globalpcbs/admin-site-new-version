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

    // Alpine.js compatible filter properties
    public $searchPartNoInput = '';
    public $searchCustomerInput = '';
    public $searchPartNo = '';
    public $searchCustomer = '';
    
    public $confirmingDelete = null;
    protected $paginationTheme = 'bootstrap';

    // SIMPLE alert properties
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

    // Alpine.js compatible search methods
    public function searchq()
    {
        $this->searchPartNo = $this->searchPartNoInput;
        $this->resetPage();
    }

    public function searchbyCustomer()
    {
        $this->searchCustomer = $this->searchCustomerInput;
        $this->resetPage();
    }

    public function filterclose()
    {
        $this->reset([
            'searchPartNoInput',
            'searchCustomerInput',
            'searchPartNo',
            'searchCustomer'
        ]);
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
    }

    public function delete($id)
    {
        stock_tb::where('stkid', $id)->delete();
        DB::table('stock_ret')->where('stkid', $id)->delete(); // If you still have this table
        
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
            ->when($this->searchCustomer, function ($q) {
                $q->whereHas('customer', function ($q2) {
                    $q2->where('c_name', 'like', '%' . $this->searchCustomer . '%');
                });
            })
            ->with(['customer', 'vendor', 'allocations'])
            ->orderBy('stkid','asc')
            ->paginate(100);
        //dd($stocks);
        return view('livewire.misc.manage-stock', compact('stocks'))->layout('layouts.app', ['title' => 'Manage Stock']);
    }
}