<?php

namespace App\Livewire\PackingSlips;

use Livewire\Component;
use App\Models\packing_tb;
use Livewire\WithPagination;
use App\Models\packing_items_tb as items;
use App\Models\profile_tb3;
use App\Models\temp_profile2;
use App\Models\maincont_packing;
use App\Models\data_tb;
use App\Models\shipper_tb;
use App\Models\maincont_tb;
use Illuminate\Support\Facades\DB;
use App\Models\order_tb     as Order;


class Manage extends Component
{
    use WithPagination;
    
    // for delete ..
    public $confirmingDelete = false;
    public $deleteId = null;
    // for search ..
    public $partSearchInput = '';
    public $customerSearchInput = '';
    public $searchPartNo = '';
    public $searchCustomer = '';
    // for search ...
    public $searchPartNoInput = '';
    public $matches    = [];          // array of suggestions ⬅️  NEW
    public $matches_partno = []; // array of part no ..
    public $searchCustomerInput = '';

    // Reset pagination when filters change
        // SIMPLE alert properties
    public $alertMessage = '';
    public $alertType = '';
    protected $listeners = ['alert-hidden' => 'clearAlert'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }
    public function updatingSearchPartNo()
    {
        $this->resetPage();
    }

    public function updatingSearchCustomer()
    {
        $this->resetPage();
    }

    public function searchByPartNo()
    {
        $this->searchPartNo = $this->partSearchInput;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->partSearchInput = '';
        $this->customerSearchInput = '';
        $this->searchPartNo = '';
        $this->searchCustomer = '';
        $this->resetPage();
    }

    public function render()
    {
        $packingSlips = packing_tb::query()
            ->when($this->searchPartNo, function ($query) {
                $query->where('part_no', 'like', '%' . $this->searchPartNo . '%');
            })
            ->when($this->searchCustomer, function ($query) {
                $query->where('customer', 'like', '%' . $this->searchCustomer . '%');
            })
            ->orderBy('invoice_id', 'desc')
            ->paginate(100);

        return view('livewire.packing-slips.manage', [
            'packingSlips' => $packingSlips,
        ])->layout('layouts.app', ['title' => 'Manage Packing Slips']);
    }
    // for delete with confirmation ..


    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function deletePackingSlip()
    {
        packing_tb::findOrFail($this->deleteId)->delete();

        $this->confirmingDelete = false;
        $this->deleteId = null;

         // SIMPLE: Just set the alert
        $this->alertMessage = 'Packing Slip Deleted successfully.';
        $this->alertType = 'danger';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
    }
    // for replica ..
    public function duplicate($id)
    {
        DB::beginTransaction();

        try {
            // Get the original packing slip
            $original = packing_tb::findOrFail($id);

            // Duplicate main packing slip
            $duplicate = $original->replicate();
            $duplicate->podate = now()->format('m/d/Y');
            $duplicate->date1 = now()->toDateTimeString();
            $duplicate->dweek = substr($duplicate->date1, 11); // extract week from new date1
            $duplicate->save();

            $newInvoiceId = $duplicate->invoice_id;

            // Duplicate all related items
            $originalItems = items::where('pid', $id)->get();

            foreach ($originalItems as $item) {
                items::create([
                    'item' => $item->item,
                    'itemdesc' => $item->itemdesc,
                    'qty2' => $item->qty2,
                    'shipqty' => $item->shipqty,
                    'pid' => $newInvoiceId,
                ]);
            }

            // Duplicate all main contacts
            $originalContacts = maincont_packing::where('packingid', $id)->get();

            foreach ($originalContacts as $contact) {
                maincont_packing::create([
                    'maincontid' => $contact->maincontid,
                    'packingid' => $newInvoiceId,
                ]);
            }

            DB::commit();

             // SIMPLE: Just set the alert
            $this->alertMessage = 'Packing Slip duplicated successfully.';
            $this->alertType = 'success';
            
            // Clear alert after a short delay by forcing a re-render
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('warning', 'Duplication failed: ' . $e->getMessage());
        }

    }
    // invoice confirmation ..
    public function togglePending($invoiceId)
    {
        $packing = packing_tb::findOrFail($invoiceId);

        // Toggle logic
        $packing->pending = ($packing->pending === 'Yes') ? 'No' : 'Yes';
        $packing->save();

         // SIMPLE: Just set the alert
        $this->alertMessage = 'Pending status updated successfully.';
        $this->alertType = 'success';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
    }
    public function isLogged($id){
        return redirect(route('packing.loggedin',$id));
    }
    // search ...
    public function searchq(){
         // assign the input values to the actual search vars
        $this->searchPartNo = $this->searchPartNoInput;
       // dd($this->partSearchInput);
        //  dd($this->searchPartNo);
            // reset pagination
        $this->resetPage();

        // clear the input fields (but keep actual filters intact)
       $this->reset(['searchPartNoInput']);  
    }
    public function searchbyCustomer() {
        $customer = data_tb::where('c_name',$this->searchCustomerInput)->first();
       // dd($customer->data_id);
        $this->searchCustomer = $customer->data_id;
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
        $this->matches = data_tb::query()
            ->where('c_name', 'like', "%{$value}%")
            ->get()
            ->toArray();
        //dd($this->matches);
    }   
    public function useMatch($i){
       // dd($this->matches[$i]['data_id']);
        $this->searchCustomerInput = $this->matches[$i]['c_name'];
        $this->matches = [];
    }
    public function usekeyupno(string $value){
         if (mb_strlen(trim($value)) < 2) {
            $this->matches_partno = [];
            return;
        }
        $this->matches_partno = packing_tb::query()
        ->select('part_no')
        ->where('part_no', 'like', "%{$value}%")
        ->get()
        ->toArray();
    }
    public function useMatchpn($i){
        $this->searchPartNoInput = $this->matches_partno[$i]['part_no'];
        $this->matches_partno = [];
    }
        public function resetFilters()
    {
        $this->reset(['searchPartNo', 'searchCustomer']);
    }
}