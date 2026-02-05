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
    
    // Alpine.js compatible filter properties
    public $searchPartNoInput = '';
    public $searchCustomerInput = '';
    public $searchPartNo = '';
    public $searchCustomer = '';

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

        $this->alertMessage = 'Confirmation Order Deleted successfully.';
        $this->alertType = 'danger';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
    }

    public function duplicate($poid)
    {
        DB::transaction(function () use ($poid) {
            $original = corder_tb::where('poid', $poid)->first();
            if (!$original) return;
        
            $copy = $original->replicate();
            $copy->podate = now()->format('m/d/Y');
            if ($copy->date2 === '0000-00-00' || empty($copy->date2)) {
                $copy->date2 = null;
            }
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

        $this->alertMessage = 'Confirmation Order duplicated successfully.';
        $this->alertType = 'success';
        
        $this->dispatch('refresh-component');
    }

    // Alpine.js compatible search methods
    public function searchq()
    {
        $this->searchPartNo = $this->searchPartNoInput;
        $this->reset(['searchPartNoInput']); // Clear the input
        $this->resetPage();
    }

    public function searchbyCustomer()
    {
        $this->searchCustomer = $this->searchCustomerInput;
        $this->reset(['searchCustomerInput']); // Clear the input
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

    public function render()
    {
        $query = corder_tb::query()
            ->select('poid', 'our_ord_num as conf_no', 'customer', 'part_no', 'rev', 'podate')
            ->orderByDesc('poid');

        if (!empty($this->searchPartNo)) {
            $query->where('part_no', 'like', '%' . $this->searchPartNo . '%');
        }

        if (!empty($this->searchCustomer)) {
            $query->where('customer', 'like', '%' . $this->searchCustomer . '%');
        }

        $orders = $query->paginate(50);

        return view('livewire.confirmation-orders.manage', [
            'orders' => $orders
        ])->layout('layouts.app', ['title' => 'Confirmation Orders']);
    }
}