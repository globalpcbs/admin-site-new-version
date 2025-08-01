<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\data_tb as Customer;

class EditCustomers extends Component
{
    public $customerId;
    public $c_name, $c_shortname, $c_email, $c_address, $c_address2, $c_address3;
    public $c_phone, $c_fax, $c_website, $c_bcontact;
    public $e_name, $e_lname, $e_phone, $e_email, $e_payment, $e_comments, $e_other, $e_cid;

    public function mount($id)
    {
        $this->customerId = $id;

        $customer = Customer::findOrFail($id);

        $this->c_name       = $customer->c_name;
        $this->c_shortname  = $customer->c_shortname;
        $this->c_email      = $customer->c_email;
        $this->c_address    = $customer->c_address;
        $this->c_address2   = $customer->c_address2;
        $this->c_address3   = $customer->c_address3;
        $this->c_phone      = $customer->c_phone;
        $this->c_fax        = $customer->c_fax;
        $this->c_website    = $customer->c_website;
        $this->c_bcontact   = $customer->c_bcontact;
        $this->e_name       = $customer->e_name;
        $this->e_lname      = $customer->e_lname;
        $this->e_phone      = $customer->e_phone;
        $this->e_email      = $customer->e_email;
        $this->e_payment    = $customer->e_payment;
        $this->e_comments   = $customer->e_comments;
        $this->e_other      = $customer->e_other;
        $this->e_cid        = $customer->e_cid;
    }

    public function update()
    {
        $this->validate([
            'c_name' => 'required|string',
            'c_email' => 'required|email',
            'c_phone' => 'required|string',
        ]);

        $customer = Customer::findOrFail($this->customerId);

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

        session()->flash('success', 'Customer updated successfully!');
        return redirect(route('manage-customers'));

    }

    public function render()
    {
        return view('livewire.customers.edit-customers')->layout('layouts.app', ['title' => 'Edit Customer']);
    }
}