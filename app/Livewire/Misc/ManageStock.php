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

    public $search = '';
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

    public function updatingSearch() { $this->resetPage(); }
    public function updatingSearchCustomer() { $this->resetPage(); }

    public function performSearch()
    {
        // This is intentionally left blank.
        // It triggers Livewire to re-render based on current public properties ($search, $searchCustomer)
    }
    public function resetFilters()
    {
        $this->reset(['search', 'searchCustomer']);
    }
    public function confirmDelete($id)
    {
        $this->confirmingDelete = $id;
    }

    public function delete($id)
    {
        stock_tb::where('stkid', $id)->delete();
        DB::table('stock_ret')->where('stkid', $id)->delete(); // If you still have this table
        // SIMPLE: Just set the alert
        $this->alertMessage = 'Stock deleted successfully.';
        $this->alertType = 'danger';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
    }

    public function duplicate($id)
    {
        $stock = stock_tb::findOrFail($id)->replicate();
        $stock->save();
         // SIMPLE: Just set the alert
        $this->alertMessage = 'Stock duplicated successfully.';
        $this->alertType = 'success';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
    }

    public function render()
    {
        $stocks = stock_tb::query()
            ->when($this->search, fn($q) => $q->where('part_no', 'like', '%' . $this->search . '%'))
            ->when($this->searchCustomer, function ($q) {
                $q->whereHas('customer', function ($q2) {
                    $q2->where('c_name', 'like', '%' . $this->searchCustomer . '%');
                });
            })
            ->with(['customer', 'vendor'])
            ->orderBy('stkid','asc')
            ->paginate(100);

        return view('livewire.misc.manage-stock', compact('stocks'))->layout('layouts.app', ['title' => 'Manage Stock']);
    }
}