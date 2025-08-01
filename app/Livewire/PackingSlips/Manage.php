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

    // Reset pagination when filters change
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

    public function searchByCustomer()
    {
        $this->searchCustomer = $this->customerSearchInput;
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

        session()->flash('warning', 'Packing Slip deleted successfully.');
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

            session()->flash('success', 'Packing Slip duplicated successfully.');

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

        session()->flash('success', 'Pending status updated.');
    }
    public function isLogged($id){
        return redirect(route('packing.loggedin',$id));
    }

}