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
    // for functional search ...
    public $searchPartNoInput = '';
    public $matches    = [];          // array of suggestions ⬅️  NEW
    public $matches_partno = []; // array of part no ..
    public $searchCustomerInput = '';
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
                    $copy->date2 = null; // or now()->format('Y-m-d') if you want today's date
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

              // SIMPLE: Just set the alert
            $this->alertMessage = 'Confirmation Order duplicated successfully.';
            $this->alertType = 'success';
            
            // Clear alert after a short delay by forcing a re-render
            $this->dispatch('refresh-component');
        }

    public function searchByPartNo()
    {
        $this->customerSearchInput = '';
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
    public function searchq(){
         // assign the input values to the actual search vars
        $this->partSearchInput = $this->searchPartNoInput;
       // dd($this->partSearchInput);
        //  dd($this->searchPartNo);
            // reset pagination
        $this->resetPage();

        // clear the input fields (but keep actual filters intact)
       $this->reset(['searchPartNoInput']);  
    }

    public function searchbyCustomer() {
       // dd($this->searchCustomerInput);
        $this->customerSearchInput = $this->searchCustomerInput;
       // reset pagination
       $this->resetPage();

        // clear the input fields (but keep actual filters intact)
       $this->reset(['searchCustomerInput']);    
    }
        // search ...
    public function onKeyUp(string $value){
       // dd($value);
         if (mb_strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }
        $this->matches = corder_tb::query()
            ->select('customer')
            ->where('customer', 'like', "%{$value}%")->distinct()
            ->get()
            ->toArray();
        //dd($this->matches);
    }   
    public function useMatch($i){
        $this->searchCustomerInput = $this->matches[$i]['customer'];
        $this->matches = [];
    }
    public function usekeyupno(string $value)
    {
        if (mb_strlen(trim($value)) < 2) {
            $this->matches_partno = [];
            return;
        }

        $this->matches_partno = corder_tb::query()
            ->select('part_no')
            ->where('part_no', 'like', "%{$value}%")
            ->distinct()
            ->get()
            ->toArray();
    }
    public function useMatchpn($i)
    {
        $this->searchPartNoInput = $this->matches_partno[$i]['part_no'];
        $this->matches_partno = [];
    }
    public function resetFilters()
    {
        $this->reset(['partSearchInput', 'customerSearchInput']);
    }
}