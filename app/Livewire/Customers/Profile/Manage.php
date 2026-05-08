<?php

namespace App\Livewire\Customers\Profile;

use App\Models\data_tb as Customer;
use App\Models\profile_tb as Profile;
use App\Models\profile_tb2 as ProfileDetail;
use Livewire\Component;
use Livewire\WithPagination;

class Manage extends Component
{
    use WithPagination;

    public $confirmingDelete = false;
    public $deleteId;
    
    // Filter property
    public $selectedCustomer = '';
    
    // Alert properties
    public $alertMessage = '';
    public $alertType = '';
    protected $listeners = ['alert-hidden' => 'clearAlert', 'refresh-component' => '$refresh'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function filterCustomers($customerId)
    {
        $this->selectedCustomer = $customerId;
        $this->resetPage();
    }

    public function updatingSelectedCustomer()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->deleteId = $id;
    }

    public function deleteProfile($id = null)
    {
        $profileId = $id ?? $this->deleteId;
        
        $profile = Profile::find($profileId);
        if ($profile) {
            ProfileDetail::where('profid', $profile->profid)->delete();
            $profile->delete();
            
            $this->alertMessage = 'Profile deleted successfully.';
            $this->alertType = 'success';
        } else {
            $this->alertMessage = 'Profile not found.';
            $this->alertType = 'danger';
        }
        
        $this->confirmingDelete = false;
        $this->deleteId = null;
        $this->dispatch('refresh-component');
    }

    public function render()
    {
        // Get all customers for the filter dropdown
        $customers = Customer::select('data_id', 'c_name')
            ->orderBy('c_name', 'asc')
            ->get();
        
        // Get profiles with optional customer filtering
        $profiles = Profile::with('customer', 'details')
            ->join('data_tb', 'profile_tb.custid', '=', 'data_tb.data_id')
            ->when($this->selectedCustomer, function ($query) {
                $query->where('profile_tb.custid', $this->selectedCustomer);
            })
            ->orderBy('data_tb.c_name', 'asc')
            ->select('profile_tb.*', 'data_tb.c_name as customer_name')
            ->paginate(10);

        return view('livewire.customers.profile.manage', [
            'profiles' => $profiles,
            'customers' => $customers
        ])->layout('layouts.app', ['title' => 'Manage Customer Profiles']);
    }
}