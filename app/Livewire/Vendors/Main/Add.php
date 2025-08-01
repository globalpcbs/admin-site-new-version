<?php

namespace App\Livewire\Vendors\Main;

use App\Models\vendor_maincont_tb as VendorMainContact;
use App\Models\vendor_tb as Vendor;
use Livewire\Component;

class Add extends Component
{
    public $cid = null;
    public $txtename;
    public $txtelname;
    public $txtephone;
    public $txteemail;
    public $txteemob;

    public $vendors = [];

    public function mount()
    {
        // Load vendors for the dropdown
        $this->vendors = Vendor::orderBy('c_name')->get();
    }

    public function save()
    {
        //dd($this->txtename);
        $this->validate([
            'cid' => 'required|numeric',
            'txtename' => 'required|string|max:255',
            'txtelname' => 'nullable|string|max:255',
            'txtephone' => 'nullable|string|max:20',
            'txteemail' => 'nullable|email|max:255',
            'txteemob' => 'nullable|string|max:20',
        ]);
        $contact = new VendorMainContact();
        $contact->coustid = $this->cid;
        $contact->name = $this->txtename;
        $contact->lastname = $this->txtelname;
        $contact->phone = $this->txtephone;
        $contact->email = $this->txteemail;
        $contact->mobile = $this->txteemob;
        $contact->save();

        session()->flash('success', 'Vendor main contact added successfully.');
        return redirect(route('vendors.main.manage'));
        //return redirect()->route('admin.vendors.main'); // adjust route if needed
    }

    public function render()
    {
        return view('livewire.vendors.main.add')
            ->layout('layouts.app', ['title' => 'Add Vendor Main Contact']);
    }
}