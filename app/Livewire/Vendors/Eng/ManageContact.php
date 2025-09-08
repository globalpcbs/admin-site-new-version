<?php

namespace App\Livewire\Vendors\Eng;

use Livewire\Component;
use Livewire\WithPagination;
use DB;

class ManageContact extends Component
{
    use WithPagination;

    public $selectedVendor = null;
    public $confirmingDelete = false;
    public $deleteId = null;

    public function filterVendors($vendorId)
    {
        $this->selectedVendor = $vendorId;
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->deleteId = $id;
    }

    public function deleteContact()
    {
        if ($this->deleteId) {
            DB::table('vendor_enggcont_tb')->where('enggcont_id', $this->deleteId)->delete();
            session()->flash('warning', 'Contact deleted successfully.');
            $this->confirmingDelete = false;
            $this->deleteId = null;
            $this->resetPage();
        }
    }

    public function render()
    {
        $contacts = DB::table('vendor_enggcont_tb')
            ->join('vendor_tb', 'vendor_enggcont_tb.coustid', '=', 'vendor_tb.data_id')
            ->when($this->selectedVendor, function ($query) {
                return $query->where('vendor_enggcont_tb.coustid', $this->selectedVendor);
            })
            ->select('vendor_enggcont_tb.*', 'vendor_tb.c_name as vendor')
            ->orderByDesc('vendor_enggcont_tb.enggcont_id')
            ->paginate(10);

        $vendorList = DB::table('vendor_tb')->select('data_id', 'c_name')->orderby('c_name','asc')->get();

        return view('livewire.vendors.eng.manage-contact', compact('contacts', 'vendorList'))
            ->layout('layouts.app', ['title' => 'Manage Vendors Eng Contact']);
    }
}
