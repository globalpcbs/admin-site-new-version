<?php

namespace App\Livewire\Customers\Profile;

use App\Models\data_tb as Customer;
use App\Models\profile_tb as Profile;
use App\Models\profile_tb2 as ProfileDetail;
use Livewire\Component;

class Edit extends Component
{
    public $profileId;
    public $cid;
    public $customers = [];
    public $requirements = [];
    public $customer_id;
    public $profile;

    public function mount($id)
    {
        $this->profileId = $id;
        $this->customers = Customer::orderBy('c_name')->get(); // FIXED: lowercase 'customers'

        $profile = Profile::with(['details', 'customer'])->findOrFail($id);
        $this->profile = $profile;
        $this->cid = $profile->custid; // FIXED: bind current customer ID to cid

        $this->requirements = $profile->details->map(function ($detail) {
            $viewableItems = explode('|', $detail->viewable);
            $checkboxes = [];
            $viewable = [];

            foreach ($viewableItems as $item) {
                if (str_ends_with($item, '1')) {
                    $checkboxes[$item] = true;
                } elseif (str_ends_with($item, '0')) {
                    $viewable[] = $item;
                }
            }

            return [
                'req' => $detail->reqs,
                'checkboxes' => $checkboxes,
                'viewable' => $viewable,
            ];
        })->toArray();
    }

    public function update()
    {
        $this->validate([
            'cid' => 'required|exists:data_tb,data_id',
            'requirements.*.req' => 'required|string',
        ]);

        $profile = Profile::findOrFail($this->profileId);
        $profile->custid = $this->cid;
        $profile->save();

        ProfileDetail::where('profid', $profile->profid)->delete();

        foreach ($this->requirements as $req) {
            $viewable = '';
            foreach ($req['viewable'] as $v) {
                $viewable .= $v . '|';
            }
            foreach ($req['checkboxes'] as $key => $val) {
                if ($val) $viewable .= $key . '|';
            }

            $detail = new ProfileDetail();
            $detail->profid = $profile->profid;
            $detail->reqs = $req['req'];
            $detail->viewable = rtrim($viewable, '|');
            $detail->save();
        }

        session()->flash('success', 'Profile updated successfully!');
        return redirect(route('customers.profile.manage'));

    }

    public function addRequirement()
    {
        $this->requirements[] = [
            'req' => '',
            'viewable' => [],
            'checkboxes' => []
        ];
    }

    public function removeRequirement($index)
    {
        unset($this->requirements[$index]);
        $this->requirements = array_values($this->requirements);
    }

    public function render()
    {
        return view('livewire.customers.profile.edit')->layout('layouts.app', ['title' => 'Edit Profile']);
    }
}