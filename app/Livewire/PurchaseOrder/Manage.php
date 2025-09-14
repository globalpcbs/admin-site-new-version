<?php

namespace App\Livewire\PurchaseOrder;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\data_tb;
use App\Models\vendor_tb;
use App\Models\porder_tb;
use App\Models\items_tb;
use App\Models\vendor_tb as Vendor;
use Illuminate\Support\Facades\DB;
use App\Models\order_tb as Order;
use App\Models\data_tb as customer;
use Carbon\Carbon;

class Manage extends Component
{
    use WithPagination;

    public $searchPart = '';
    public $searchCustomer = '';
    public $searchVendor = '';
    public $searchPartNoInput = '';
    public $searchCustomerInput = '';
    public $searchVendorInput = '';

    public $matches    = [];          // array of suggestions ⬅️  NEW
    public $matches_partno = []; // array of part no ..
    public $matches_vendor = []; // array of vendor ..
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
        porder_tb::destroy($id);
        $this->dispatch('alert', type: 'warning', message: 'Record deleted successfully!');
        
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
            $this->dispatch('alert', type: 'success', message: 'Purchase Order duplicated successfully!');

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
                $q->where('c_shortname', 'like', '%' . $this->searchVendor . '%');
            });
        }

        $orders = $query->paginate(1000);

        return view('livewire.purchase-order.manage', compact('orders'))->layout('layouts.app');
    }
    public function searchv(){
         $this->searchVendor = $this->searchVendorInput;
      //  dd($this->searchPartNo);
        // reset pagination
       $this->resetPage();

        // clear the input fields (but keep actual filters intact)
       $this->reset(['searchVendorInput']);    
    }
    public function searchq(){
         // assign the input values to the actual search vars
        $this->searchPart = $this->searchPartNoInput;
      //  dd($this->searchPartNo);
        // reset pagination
       $this->resetPage();

        // clear the input fields (but keep actual filters intact)
       $this->reset(['searchPartNoInput']);    
    }

    public function searchbyCustomer() {
       // dd($this->searchCustomerInput);
        $this->searchCustomer = $this->searchCustomerInput;
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
        $this->matches = porder_tb::query()
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
    public function usekeyupno(string $value){
         if (mb_strlen(trim($value)) < 2) {
            $this->matches_partno = [];
            return;
        }
        $this->matches_partno = porder_tb::query()
        ->select('part_no')
        ->where('part_no', 'like', "%{$value}%")
        ->get()
        ->toArray();
    }
    public function useMatchpn($i){
        $this->searchPartNoInput = $this->matches_partno[$i]['part_no'];
        $this->matches_partno = [];
    }
    public function usekeyupvendor(string $value){
      //  dd($value);
         if (mb_strlen(trim($value)) < 2) {
            $this->matches_vendor = [];
            return;
           // dd('test not working');
        }
       $this->matches_vendor = porder_tb::with('vendor')
        ->whereHas('vendor', function ($q) use ($value) {
            $q->where('c_name', 'like', '%' . $value . '%')
            ->orWhere('c_shortname', 'like', '%' . $value . '%');
        })
        ->get()
        ->pluck('vendor')       // take only vendor relation
        ->unique('data_id')          // remove duplicates
        ->values()
        ->toArray();
    }
    public function useMatchve($i){
        $this->searchVendorInput = $this->matches_vendor[$i]['c_shortname'];
 //       dd($this->matches_vendor[$i]['data_id']);
        $this->matches_vendor = [];
    }
}