<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\data_tb as Customer;

class AddCustomers extends Component
{
    public $c_name, $c_shortname, $c_email, $c_address, $c_address2, $c_address3;
    public $c_phone, $c_fax, $c_website, $c_bcontact;
    public $e_name, $e_lname, $e_phone, $e_email, $e_payment, $e_comments, $e_other, $e_cid;

    public function submit()
    {
        $this->validate([
            'c_name' => 'required|string',
            'c_shortname' => 'nullable|string',
            'c_email' => 'required|email',
            'c_address' => 'nullable|string',
            'c_phone' => 'required|string',
            'c_fax' => 'nullable|string',
            'c_website' => 'nullable|string',
            'e_payment' => 'nullable|string',
            'e_comments' => 'nullable|string',
            'e_other' => 'nullable|string',
            'e_cid' => 'nullable|string',
        ]);

        $customer = new Customer;
        $customer->c_name       = $this->c_name;
        $customer->c_shortname  = $this->c_shortname;
        $customer->c_email      = $this->c_email;
        $customer->c_address    = $this->c_address;
        $customer->c_address2   = $this->c_address2;
        $customer->c_address3   = $this->c_address3;
        $customer->c_phone      = $this->c_phone;
        $customer->c_fax        = $this->c_fax;
        $customer->c_website    = $this->c_website;
        $customer->c_bcontact   = $this->c_bcontact;
        $customer->e_name       = $this->e_name;
        $customer->e_lname      = $this->e_lname;
        $customer->e_phone      = $this->e_phone;
        $customer->e_email      = $this->e_email;
        $customer->e_payment    = $this->e_payment;
        $customer->e_comments   = $this->e_comments;
        $customer->e_other      = $this->e_other;
        $customer->e_cid        = $this->e_cid;
        $customer->save();

        session()->flash('success', 'Customer added successfully!');
        return redirect(route('manage-customers'));
       // return redirect()->route('admin.customers.index');
    }

    public function render()
    {
        return view('livewire.customers.add-customers')->layout('layouts.app', ['title' => 'Add New Customers']);
    }
}