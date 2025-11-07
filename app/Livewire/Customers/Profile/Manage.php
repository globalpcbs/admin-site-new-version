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
        $this->confirmingDelete = true;
        $this->deleteId = $id;
    }

    public function deleteProfile($id)
    {
        $this->deleteId = $id;
        $profile = Profile::find($this->deleteId);
        if ($profile) {
            ProfileDetail::where('profid', $profile->profid)->delete();
            $profile->delete();
        }
         // SIMPLE: Just set the alert
        $this->alertMessage = 'Profile deleted successfully.';
        $this->alertType = 'warning';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
    }

    public function render()
    {
        $profiles = Profile::with('customer', 'details')
            ->orderByDesc('profid')
            ->paginate(10);

        return view('livewire.customers.profile.manage', [
            'profiles' => $profiles
        ])->layout('layouts.app', ['title' => 'Manage Customer Profiles']);
    }
}
