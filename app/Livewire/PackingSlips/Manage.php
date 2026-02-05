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
use App\Models\order_tb as Order;

class Manage extends Component
{
    use WithPagination;
    
    // for delete ..
    public $confirmingDelete = false;
    public $deleteId = null;
    
    // Alpine.js compatible filter properties
    public $searchPartNoInput = '';
    public $searchCustomerInput = '';
    public $searchPartNo = '';
    public $searchCustomer = '';

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

    // Alpine.js compatible search methods
    public function searchq()
    {
        $this->searchPartNo = $this->searchPartNoInput;
        $this->reset(['searchPartNoInput']); // Clear the input
        $this->resetPage();
    }

    public function searchbyCustomer()
    {
       // dd($this->searchCustomerInput);
       //$data_id = data_tb::where('c_name',$this->searchCustomerInput)->first();
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
        $packingSlips = packing_tb::with('custo') // eager load customer
            ->when($this->searchPartNo, function ($query) {
                $query->where('part_no', 'like', '%' . $this->searchPartNo . '%');
            })
            ->when($this->searchCustomer, function ($query) {
                $search = $this->searchCustomer;
                
                $query->whereHas('custo', function($q) use ($search) {
                    // Partial match (like 'a') or full name
                    $q->where('c_name', 'like', "%{$search}%");
                });
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

    public function deletePackingSlip($id)
    {
        $this->deleteId = $id;
        packing_tb::findOrFail($this->deleteId)->delete();

        $this->confirmingDelete = false;
        $this->deleteId = null;

        $this->alertMessage = 'Packing Slip Deleted successfully.';
        $this->alertType = 'danger';
        
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

            $this->alertMessage = 'Packing Slip duplicated successfully.';
            $this->alertType = 'success';
            
            $this->dispatch('refresh-component');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->alertMessage = 'Duplication failed: ' . $e->getMessage();
            $this->alertType = 'danger';
        }
    }

    // invoice confirmation ..
    public function togglePending($invoiceId)
    {
        $packing = packing_tb::findOrFail($invoiceId);

        // Toggle logic
        $packing->pending = ($packing->pending === 'Yes') ? 'No' : 'Yes';
        $packing->save();

        $this->alertMessage = 'Pending status updated successfully.';
        $this->alertType = 'success';
        
        $this->dispatch('refresh-component');
    }

    public function isLogged($id){
        return redirect(route('packing.loggedin',$id));
    }
}