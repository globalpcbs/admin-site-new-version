<?php

namespace App\Livewire\ConfirmationOrders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\corder_tb;
use App\Models\citems_tb;
use App\Models\mdlitems_tb;
use App\Models\data_tb      as Customer;
use App\Models\shipper_tb   as Shipper;
use App\Models\order_tb     as Order;
use Illuminate\Support\Facades\DB;

class Manage extends Component
{
    use WithPagination;

    public $confirmingDeleteId = null;
    public $partSearchInput = '';
    public $customerSearchInput = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingPartSearchInput()
    {
        $this->resetPage();
    }

    public function updatingCustomerSearchInput()
    {
        $this->resetPage();
    }

    public function delete($poid)
    {
        DB::transaction(function () use ($poid) {
            citems_tb::where('pid', $poid)->delete();
            mdlitems_tb::where('pid', $poid)->delete();
            corder_tb::where('poid', $poid)->delete();
        });

        session()->flash('success', 'Order deleted successfully.');
    }

    public function duplicate($poid)
    {
        DB::transaction(function () use ($poid) {
            $original = corder_tb::where('poid', $poid)->first();
            if (!$original) return;

            $copy = $original->replicate();
            $copy->podate = now()->format('m/d/Y');
            $copy->save();

            $newPoid = $copy->poid;

            $items = citems_tb::where('pid', $poid)->get();
            foreach ($items as $item) {
                $newItem = $item->replicate();
                $newItem->pid = $newPoid;
                $newItem->save();
            }

            $deliveries = mdlitems_tb::where('pid', $poid)->get();
            foreach ($deliveries as $delivery) {
                $newDelivery = $delivery->replicate();
                $newDelivery->pid = $newPoid;
                $newDelivery->save();
            }
        });

        session()->flash('success', 'Order duplicated successfully.');
    }

    public function searchByPartNo()
    {
        $this->customerSearchInput = '';
        $this->resetPage();
    }

    public function searchByCustomer()
    {
        $this->partSearchInput = '';
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->partSearchInput = '';
        $this->customerSearchInput = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = corder_tb::query()
            ->select('poid', 'our_ord_num as conf_no', 'customer', 'part_no', 'rev', 'podate')
            ->orderByDesc('poid');

        if (!empty($this->partSearchInput)) {
            $query->where('part_no', 'like', '%' . $this->partSearchInput . '%');
        }

        if (!empty($this->customerSearchInput)) {
            $query->where('customer', 'like', '%' . $this->customerSearchInput . '%');
        }

        $orders = $query->paginate(50);

        return view('livewire.confirmation-orders.manage', [
            'orders' => $orders
        ])->layout('layouts.app', ['title' => 'Confirmation Orders']);
    }
}