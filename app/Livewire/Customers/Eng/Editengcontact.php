<?php

namespace App\Livewire\Customers\Eng;

use Livewire\Component;
use App\Models\data_tb as Customer;
use App\Models\enggcont_tb;

class Editengcontact extends Component
{
    public $enggcont_id;
    public $cid;
    public $txtename;
    public $txtelname;
    public $txtephone;
    public $txteemail;
    public $txteemob;

    public $customers = [];

    public function mount($id)
    {
        $this->enggcont_id = $id;
        $contact = enggcont_tb::findOrFail($id);

        $this->cid        = $contact->coustid;
        $this->txtename   = $contact->name;
        $this->txtelname  = $contact->lastname;
        $this->txtephone  = $contact->phone;
        $this->txteemail  = $contact->email;
        $this->txteemob   = $contact->mobile;

        $this->customers = Customer::orderBy('c_name')->get();
    }

    public function save()
    {
        $this->validate([
            'cid' => 'required|integer',
            'txtename' => 'required|string|max:100',
            'txtelname' => 'nullable|string|max:100',
            'txtephone' => 'required|string|max:50',
            'txteemail' => 'required|email|max:150',
            'txteemob' => 'nullable|string|max:50',
        ]);

        $contact = enggcont_tb::findOrFail($this->enggcont_id);
        $contact->coustid  = $this->cid;
        $contact->name     = $this->txtename;
        $contact->lastname = $this->txtelname;
        $contact->phone    = $this->txtephone;
        $contact->email    = $this->txteemail;
        $contact->mobile   = $this->txteemob;
        $contact->save();

        session()->flash('success', 'Engineering Contact updated successfully!');
        return redirect(route('customers.eng.manage'));
    }

    public function render()
    {
        return view('livewire.customers.eng.editengcontact')->layout('layouts.app', ['title' => 'Edit Engineering Contact']);
    }
}