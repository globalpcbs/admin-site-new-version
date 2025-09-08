<?php 
namespace App\Livewire\Vendors\Main;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\vendor_maincont_tb as VendorMainContact;
use App\Models\vendor_tb as Vendor;

class Manage extends Component
{
    use WithPagination;

    public $confirmingDelete = false;
    public $deleteId = null;
    public $selectedVendor = null;

    public function filterVendors($vendorId)
    {
        $this->selectedVendor = $vendorId;
        $this->resetPage(); // go to page 1 when filter changes
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteContact()
    {
        VendorMainContact::findOrFail($this->deleteId)->delete();
        session()->flash('warning', 'Vendor contact deleted successfully!');
        $this->confirmingDelete = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $query = VendorMainContact::query();

        if ($this->selectedVendor) {
            $query->where('coustid', $this->selectedVendor); // Correct column name here
        }

        $contacts = $query->orderBy('enggcont_id', 'desc')->paginate(15);
        $vendorList = Vendor::orderBy('c_name')->orderby('c_name','asc')->get();

        return view('livewire.vendors.main.manage', compact('contacts', 'vendorList'))
            ->layout('layouts.app', ['title' => 'Vendor Main Contacts']);
    }
}
