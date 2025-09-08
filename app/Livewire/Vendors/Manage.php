<?php 
namespace App\Livewire\Vendors;

use App\Models\vendor_tb as Vendor;
use Livewire\Component;
use Livewire\WithPagination;

class Manage extends Component
{
    use WithPagination;

    public $search = '';
    public $deleteId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function filterVendors($value)
    {
        $this->search = $value;
        $this->resetPage();
    }

    public function setDeleteId($id)
    {
        $this->deleteId = $id;
    }

    public function deleteVendor()
    {
        Vendor::where('data_id', $this->deleteId)->delete();
        session()->flash('message', 'Vendor deleted successfully!');
    }

    public function render()
    {
        $allVendors = Vendor::select('c_name')->distinct()->orderby('c_name','asc')->get();

        $vendors = Vendor::when($this->search, function ($query) {
            $query->where('c_name', $this->search);
        })->orderBy('data_id', 'desc')->paginate(10);

        return view('livewire.vendors.manage', [
            'vendors' => $vendors,
            'allVendors' => $allVendors,
        ])->layout('layouts.app', ['title' => 'Manage Vendors']);
    }
}
