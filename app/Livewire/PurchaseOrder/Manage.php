<?php

namespace App\Livewire\PurchaseOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\data_tb;
use App\Models\vendor_tb;
use App\Models\porder_tb;
use App\Models\items_tb;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Manage extends Component
{
    use WithPagination;

    public $searchPart = '';
    public $searchCustomer = '';
    public $searchVendor = '';

    protected $paginationTheme = 'bootstrap';

    public function updating($field)
    {
        if (in_array($field, ['searchPart', 'searchCustomer', 'searchVendor'])) {
            $this->resetPage();
        }
    }
    public function resetFilters()
    {
        $this->reset(['searchPart', 'searchCustomer', 'searchVendor']);
    }
    public function delete($id)
    {
        porder_tb::where('poid', $id)->delete();
        session()->flash('warning', 'Purchase Order Deleted Succesfully.');
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
            session()->flash('success', 'Purchase Order Duplicated Succesfully.');

        } catch (\Exception $e) {
            DB::rollBack();
             session()->flash('warning', 'Duplication failed.'.$e->getMessage());
        logger()->error('Purchase Order duplication failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = porder_tb::where('cancel', '!=', 1)->latest('poid');

        if ($this->searchCustomer) {
            $query->where('customer', 'like', '%' . $this->searchCustomer . '%');
        }

        if ($this->searchPart) {
            $query->where('part_no', 'like', '%' . $this->searchPart . '%');
        }

        if ($this->searchVendor) {
            $query->whereHas('vendor', function ($q) {
                $q->where('c_name', 'like', '%' . $this->searchVendor . '%');
            });
        }

        $orders = $query->paginate(1000);

        return view('livewire.purchase-order.manage', compact('orders'))->layout('layouts.app');
    }
}