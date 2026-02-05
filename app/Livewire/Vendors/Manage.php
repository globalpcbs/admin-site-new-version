<?php 
namespace App\Livewire\Vendors;

use App\Models\vendor_tb as Vendor;
use Livewire\Component;
use Livewire\WithPagination;

class Manage extends Component
{
    use WithPagination;

    public $search = '';
    public $showAlert = false;
    public $alertMessage = '';
    public $alertType = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function filterVendors($value)
    {
        $this->search = $value;
        $this->resetPage();
    }

    public function deleteVendor($id)
    {
        Vendor::where('data_id', $id)->delete();
        
        // Set alert variables
        $this->showAlert = true;
        $this->alertMessage = 'Vendor deleted successfully!';
        $this->alertType = 'warning';
        
        // Auto-hide alert after 3 seconds
        $this->dispatch('hide-alert-after-delay');
    }

    public function hideAlert()
    {
        $this->showAlert = false;
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function render()
    {
        $allVendors = Vendor::select('c_name')
        ->orderByRaw('LOWER(c_name) asc')
        ->get();
       // dd($allVendors);
        $vendors = Vendor::when($this->search, function ($query) {
            $query->where('c_name', $this->search);
        })->orderBy('data_id', 'desc')->paginate(10);

        return view('livewire.vendors.manage', [
            'vendors' => $vendors,
            'allVendors' => $allVendors,
        ])->layout('layouts.app', ['title' => 'Manage Vendors']);
    }
}