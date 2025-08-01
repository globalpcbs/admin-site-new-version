<?php

namespace App\Livewire\Customers\Main;

use App\Models\maincont_tb as MainContact;
use App\Models\data_tb as Customer;
use Livewire\Component;

class Editmaincustomer extends Component
{
    public $contactId;
    public $cid;
    public $txtename;
    public $txtelname;
    public $txtephone;
    public $txteemail;
    public $txteemob;

    public $customers = [];

    public function mount($id)
    {
        $this->contactId = $id;
        $contact = MainContact::findOrFail($id);

        $this->cid = $contact->coustid;
        $this->txtename = $contact->name;
        $this->txtelname = $contact->lastname;
        $this->txtephone = $contact->phone;
        $this->txteemail = $contact->email;
        $this->txteemob = $contact->mobile;

        $this->customers = Customer::orderBy('c_name')->get();
    }

    public function update()
    {
        $this->validate([
            'cid' => 'required|exists:data_tb,data_id',
            'txtename' => 'required|string|max:255',
            'txtelname' => 'required|string|max:255',
            'txtephone' => 'nullable|string|max:20',
            'txteemail' => 'nullable|email|max:255',
            'txteemob' => 'nullable|string|max:20',
        ]);

        $contact = MainContact::findOrFail($this->contactId);
        $contact->name = $this->txtename;
        $contact->lastname = $this->txtelname;
        $contact->phone = $this->txtephone;
        $contact->email = $this->txteemail;
        $contact->mobile = $this->txteemob;
        $contact->coustid = $this->cid;
        $contact->save();

        session()->flash('success', 'Main Contact updated successfully!');
        return redirect(route('customers.main.manage'));

    }

    public function render()
    {
        return view('livewire.customers.main.editmaincustomer')
            ->layout('layouts.app', ['title' => 'Edit Main Contact']);
    }
}