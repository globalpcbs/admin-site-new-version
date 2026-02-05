<?php

namespace App\Livewire\PurchaseOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\porder_tb;
use App\Models\items_tb;
use App\Models\vendor_tb;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Manage extends Component
{
    use WithPagination;

    public $searchPart = '';
    public $searchCustomer = '';
    public $searchVendor = '';

    protected $paginationTheme = 'bootstrap';
    
    public $alertMessage = '';
    public $alertType = '';

    protected $listeners = ['alert-hidden' => 'clearAlert'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function updating($field)
    {
        if (in_array($field, ['searchPart', 'searchCustomer', 'searchVendor'])) {
            $this->resetPage();
        }
    }

    public function resetFilters()
    {
        $this->reset([
            'searchPart', 'searchCustomer', 'searchVendor'
        ]);
        $this->resetPage();
        
        // Dispatch event to reset Alpine.js components
        $this->dispatch('resetFiltersCompleted');
    }

    public function delete($id)
    {
        //dd($id);
        porder_tb::destroy($id);
        $this->alertMessage = 'Purchase Order deleted successfully.';
        $this->alertType = 'warning';
    }

    public function duplicate($id)
    {
        DB::beginTransaction();
        try {
            $original = porder_tb::findOrFail($id);
            $newPo = $original->replicate();
            $newPo->note = null;
            $newPo->supli_due = null;
            $newPo->cus_due = null;
            $newPo->podate = Carbon::now()->format('m/d/Y');
            $newPo->save();

            $originalItems = items_tb::where('pid', $id)->get();

            foreach ($originalItems as $item) {
                $newItem = $item->replicate();
                $newItem->pid = $newPo->poid;
                $newItem->save();
            }

            DB::commit();
            $this->alertMessage = 'Purchase Order duplicated successfully.';
            $this->alertType = 'success';

        } catch (\Exception $e) {
            DB::rollBack();
            $this->alertMessage = 'Duplication failed: ' . $e->getMessage();
            $this->alertType = 'warning';
        }
    }

    public function render()
    {
        $query = porder_tb::where('cancel', '!=', 1)->latest('poid');

        if (!empty($this->searchCustomer)) {
            $query->where('customer', 'like', '%' . $this->searchCustomer . '%');
        }

        if (!empty($this->searchPart)) {
            $query->where('part_no', 'like', '%' . $this->searchPart . '%');
        }

        if (!empty($this->searchVendor)) {
            $query->whereHas('vendor', function ($q) {
                $q->where('c_shortname', 'like', '%' . $this->searchVendor . '%')
                  ->orWhere('c_name', 'like', '%' . $this->searchVendor . '%');
            });
        }

        $orders = $query->paginate(50);

        return view('livewire.purchase-order.manage', compact('orders'))->layout('layouts.app');
    }
}