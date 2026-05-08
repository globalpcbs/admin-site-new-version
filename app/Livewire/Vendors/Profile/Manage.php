<?php

namespace App\Livewire\Vendors\Profile;

use App\Models\profile_vendor_tb as ProfileVendor;
use App\Models\profile_vendor_tb2 as ProfileVendor2;
use App\Models\vendor_tb as Vendor;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Manage extends Component
{
    use WithPagination;

    public $confirmingDelete = false;
    public $deleteId;
    
    // Filter property
    public $selectedVendor = '';

    // Alert properties
    public $alertMessage = '';
    public $alertType = '';
    protected $listeners = ['alert-hidden' => 'clearAlert', 'refresh-component' => '$refresh'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function filterVendors($vendorId)
    {
        $this->selectedVendor = $vendorId;
        $this->resetPage();
    }

    public function updatingSelectedVendor()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteVendorProfile($id = null)
    {
        $profileId = $id ?? $this->deleteId;
        
        DB::transaction(function () use ($profileId) {
            ProfileVendor2::where('profid', $profileId)->delete();
            ProfileVendor::where('profid', $profileId)->delete();
        });

        $this->alertMessage = 'Vendor profile deleted successfully.';
        $this->alertType = 'success';
        
        $this->confirmingDelete = false;
        $this->deleteId = null;
        $this->dispatch('refresh-component');
    }

    public function render()
    {
        // Get all vendors for the filter dropdown
        $vendorsList = Vendor::select('data_id', 'c_name')
            ->orderBy('c_name', 'asc')
            ->get();
        
        // Get vendor profiles with optional filtering
        $vendors = ProfileVendor::with([
            'requirements', // Relationship in ProfileVendor: hasMany/hasOne to ProfileVendor2
            'vendor'        // Relationship in ProfileVendor: belongsTo Vendor
        ])
        ->join('vendor_tb', 'profile_vendor_tb.custid', '=', 'vendor_tb.data_id')
        ->when($this->selectedVendor, function ($query) {
            $query->where('profile_vendor_tb.custid', $this->selectedVendor);
        })
        ->orderBy('vendor_tb.c_name', 'asc') // Alphabetical order by vendor name
        ->select('profile_vendor_tb.*', 'vendor_tb.c_name as vendor_name')
        ->paginate(10);

        return view('livewire.vendors.profile.manage', compact('vendors', 'vendorsList'))
            ->layout('layouts.app', ['title' => 'Manage Vendors Profile']);
    }
}