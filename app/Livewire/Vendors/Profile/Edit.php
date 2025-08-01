<?php

namespace App\Livewire\Vendors\Profile;

use App\Models\Profile_vendor_tb as ProfileVendor;
use App\Models\Profile_vendor_tb2 as ProfileVendor2;
use App\Models\vendor_tb as Vendor;
use Livewire\Component;

class Edit extends Component
{
    public $profid;
    public $cid;
    public $requirements = [''];

    public function mount($profid)
    {
        $this->profid = $profid;

        $profile = ProfileVendor::with('requirements')->findOrFail($profid);
        $this->cid = $profile->custid;
        $this->requirements = $profile->requirements->pluck('reqs')->toArray();

        if (empty($this->requirements)) {
            $this->requirements = [''];
        }
    }

    public function addRequirement()
    {
        if (count($this->requirements) >= 20) {
            $this->dispatchBrowserEvent('alert', ['message' => 'Only 20 requirements are allowed.']);
            return;
        }
        $this->requirements[] = '';
    }

    public function removeRequirement($index)
    {
        if (count($this->requirements) > 1) {
            unset($this->requirements[$index]);
            $this->requirements = array_values($this->requirements);
        }
    }

    public function save()
    {
        $this->validate([
            'cid' => 'required|exists:vendor_tb,data_id',
            'requirements.*' => 'nullable|string|max:255',
        ]);

        $profile = ProfileVendor::findOrFail($this->profid);
        $profile->update(['custid' => $this->cid]);

        ProfileVendor2::where('profid', $this->profid)->delete();

        foreach ($this->requirements as $req) {
            if (trim($req) !== '') {
                ProfileVendor2::create([
                    'profid' => $this->profid,
                    'reqs' => trim($req),
                ]);
            }
        }

        session()->flash('success', 'Vendor profile updated successfully.');
        return redirect(route('vendors.profile.manage'));

       // return redirect()->route('vendors.profile.index');
    }

    public function render()
    {
        $vendors = Vendor::orderBy('c_name')->get();

        return view('livewire.vendors.profile.edit', compact('vendors'))
            ->layout('layouts.app', ['title' => 'Edit Vendor Profile']);
    }
}