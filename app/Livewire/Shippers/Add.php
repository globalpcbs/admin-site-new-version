<?php

namespace App\Livewire\Shippers;

use Livewire\Component;
use App\Models\shipper_tb as Shipper;

class Add extends Component
{
    public $c_name, $c_address, $c_address2, $c_address3, $c_phone, $c_fax, $c_website,
       $c_bcontact, $e_name, $e_lname, $e_phone, $e_email, $e_payment, $e_comments, $e_other;

    public function render()
    {
        return view('livewire.shippers.add')->layout('layouts.app', ['title' => 'Add New Shipper']);
    }
     protected $rules = [
        'c_name' => 'required|string|max:255',
        'c_address' => 'nullable|string|max:255',
        'c_address2' => 'nullable|string|max:255',
        'c_address3' => 'nullable|string|max:255',
        'c_phone' => 'required|string|max:20',
        'c_fax' => 'nullable|string|max:20',
        'c_website' => 'nullable|max:255',
        'c_bcontact' => 'nullable|string|max:255',
        'e_name' => 'nullable|string|max:255',
        'e_lname' => 'nullable|string|max:255',
        'e_phone' => 'nullable|string|max:20',
        'e_email' => 'nullable|email|max:255',
        'e_payment' => 'nullable|string|max:255',
        'e_comments' => 'nullable|string|max:1000',
        'e_other' => 'nullable|string|max:1000',
    ];

    public function addShipper()
    {
        $this->validate();

        $shipper = new Shipper();
        $shipper->c_name = $this->c_name;
        $shipper->c_address = $this->c_address;
        $shipper->c_address2 = $this->c_address2;
        $shipper->c_address3 = $this->c_address3;
        $shipper->c_phone = $this->c_phone;
        $shipper->c_fax = $this->c_fax;
        $shipper->c_website = $this->c_website;
        $shipper->c_bcontact = $this->c_bcontact;
        $shipper->e_name = $this->e_name;
        $shipper->e_lname = $this->e_lname;
        $shipper->e_phone = $this->e_phone;
        $shipper->e_email = $this->e_email;
        $shipper->e_payment = $this->e_payment;
        $shipper->e_comments = $this->e_comments;
        $shipper->e_other = $this->e_other;
        $shipper->save();

        session()->flash('success', 'Shipper added successfully!');
        return redirect(route('shippers.manage'));
    }
}