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

     // SIMPLE alert properties
    public $alertMessage = '';
    public $alertType = '';
    protected $listeners = ['alert-hidden' => 'clearAlert'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteVendorProfile($id)
    {  // dd($id);
        $this->deleteId = $id;
        DB::transaction(function () {
            ProfileVendor::where('profid', $this->deleteId)->delete();
            ProfileVendor2::where('profid', $this->deleteId)->delete();
        });

         // SIMPLE: Just set the alert
        $this->alertMessage = 'Profile deleted successfully.';
        $this->alertType = 'danger';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
    }

    public function render()
    {
        $vendors = ProfileVendor::with([
                'requirements',  // Assuming relationship in ProfileVendor: hasOne(ProfileVendor2::class, 'profid', 'profid')
                'vendor'     // Assuming relationship in ProfileVendor: belongsTo(Vendor::class, 'custid', 'data_id')
            ])
            ->orderBy('profid', 'desc')
            ->paginate(10);

        return view('livewire.vendors.profile.manage', compact('vendors'))
            ->layout('layouts.app', ['title' => 'Manage Vendors Profile']);
    }
}
