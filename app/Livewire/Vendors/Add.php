<?php

namespace App\Livewire\Vendors;

use Livewire\Component;
use App\Models\vendor_tb as Vendor; // Make sure this model exists
use Illuminate\Support\Facades\DB;

class Add extends Component
{
    public $c_name, $c_shortname, $c_address, $c_address2, $c_address3;
    public $c_phone, $c_fax, $c_website, $c_bcontact;
    public $e_name, $e_lname, $e_phone, $e_email, $e_payment, $e_comments, $e_other;

    public function submit()
    {
        $this->validate([
            'c_name' => 'required|string|max:255',
            'c_shortname' => 'nullable|string|max:255',
            'c_address' => 'nullable|string|max:255',
            'c_address2' => 'nullable|string|max:255',
            'c_address3' => 'nullable|string|max:255',
            'c_phone' => 'required|nullable|string|max:50',
            'c_fax' => 'nullable|string|max:50',
            'c_website' => 'nullable|url|max:255',
            'e_payment' => 'nullable|string|max:255',
            'e_comments' => 'nullable|string',
            'e_other' => 'nullable|string',
        ]);

        $vendor = new Vendor();
        $vendor->c_name = $this->c_name;
        $vendor->c_shortname = $this->c_shortname;
        $vendor->c_address = $this->c_address;
        $vendor->c_address2 = $this->c_address2;
        $vendor->c_address3 = $this->c_address3;
        $vendor->c_phone = $this->c_phone;
        $vendor->c_fax = $this->c_fax;
        $vendor->c_website = $this->c_website;
        $vendor->c_bcontact = $this->c_bcontact;
        $vendor->e_name = $this->e_name;
        $vendor->e_lname = $this->e_lname;
        $vendor->e_phone = $this->e_phone;
        $vendor->e_email = $this->e_email;
        $vendor->e_payment = $this->e_payment;
        $vendor->e_comments = $this->e_comments;
        $vendor->e_other = $this->e_other;
        $vendor->save();

        session()->flash('success', 'Vendor added successfully.');
        return redirect(route('manage.vendor'));

    }

    public function render()
    {
        return view('livewire.vendors.add')->layout('layouts.app', ['title' => 'Add Vendor']);
    }
}