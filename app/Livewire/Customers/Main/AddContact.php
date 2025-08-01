<?php

namespace App\Livewire\Customers\Main;

use App\Models\maincont_tb as MainContact;
use App\Models\data_tb as Customer; 
use Livewire\Component;

class AddContact extends Component
{
    public $cid;
    public $txtename;
    public $txtelname;
    public $txtephone;
    public $txteemail;
    public $txteemob;

    public $customers = [];

    public function mount()
    {
        $this->customers = Customer::orderBy('c_name')->get();
    }

    public function store()
    {
        $this->validate([
            'cid' => 'required|exists:data_tb,data_id',
            'txtename' => 'required|string|max:255',
            'txtelname' => 'required|string|max:255',
            'txtephone' => 'nullable|string|max:20',
            'txteemail' => 'nullable|email|max:255',
            'txteemob' => 'nullable|string|max:20',
        ]);

        $contact = new MainContact();
        $contact->name = $this->txtename;
        $contact->lastname = $this->txtelname;
        $contact->phone = $this->txtephone;
        $contact->email = $this->txteemail;
        $contact->mobile = $this->txteemob;
        $contact->coustid = $this->cid;
        $contact->save();

        session()->flash('success', 'Main Contact added successfully!');
        return redirect(route('customers.main.manage'));
      //  return redirect()->route('admin.customers'); // or wherever you want
    }

    public function render()
    {
        return view('livewire.customers.main.add-contact')
            ->layout('layouts.app', ['title' => 'Customers Main Contact']);
    }
}