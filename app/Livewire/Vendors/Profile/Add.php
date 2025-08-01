<?php

namespace App\Livewire\Vendors\Profile;

use App\Models\Profile_vendor_tb as ProfileVendor;
use App\Models\Profile_vendor_tb2 as ProfileVendor2;
use App\Models\vendor_tb as Vendor;
use Livewire\Component;

class Add extends Component
{
    public $profid;
    public $cid;
    public $requirements = [''];

    public function mount($profid = null)
    {
        $this->profid = $profid;

        if ($this->profid) {
            $profile = ProfileVendor::with('requirements')->findOrFail($this->profid);
            $this->cid = $profile->custid;
            $this->requirements = $profile->requirements->pluck('reqs')->toArray();
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
            $this->requirements = array_values($this->requirements); // reindex
        }
    }

    public function save()
    {
        $this->validate([
            'cid' => 'required|exists:vendor_tb,data_id',
            'requirements.*' => 'nullable|string|max:255',
        ]);
    //    dd($this->profid);
        if ($this->profid) {
            dd("Work");
            $profile = ProfileVendor::findOrFail($this->profid);
            $profile->update(['custid' => $this->cid]);
            ProfileVendor2::where('profid', $this->profid)->delete();
        } else {
          //  dd($this->profid);
            $profile = new ProfileVendor();
            $profile->custid = $this->cid;
            $profile->save();
           // dd($profile->profid);
            $this->profid = $profile->profid ;
        }
        foreach ($this->requirements as $req) {
            if (trim($req) !== '') {
                ProfileVendor2::create([
                    'profid' => $this->profid,
                    'reqs' => trim($req),
                ]);
            }
        }
        session()->flash('success', 'Vendor engineering contact added successfully.');
        return redirect(route('vendors.profile.manage'));
       // return redirect()->route('vendors.profile.index');
    }

    public function render()
    {
        $vendors = Vendor::orderBy('c_name')->get();
        return view('livewire.vendors.profile.add', compact('vendors'))
            ->layout('layouts.app', ['title' => $this->profid ? 'Edit Profile' : 'Add Profile']);
    }
}