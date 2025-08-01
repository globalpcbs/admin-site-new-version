<?php

namespace App\Livewire\Vendors\Profile;

use App\Models\Profile_vendor_tb as ProfileVendor;
use App\Models\Profile_vendor_tb2 as ProfileVendor2;
use App\Models\vendor_tb as Vendor;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Manage extends Component
{
    use WithPagination;

    public $confirmingDelete = false;
    public $deleteId;

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteVendorProfile()
    {
        DB::transaction(function () {
            ProfileVendor::where('profid', $this->deleteId)->delete();
            ProfileVendor2::where('profid', $this->deleteId)->delete();
        });

        session()->flash('warning', 'Vendor profile deleted successfully.');
        $this->confirmingDelete = false;
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
