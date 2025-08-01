<?php 
namespace App\Livewire\Customers\Eng;

use Livewire\Component;
use App\Models\data_tb as Customer;
use App\Models\enggcont_tb; // Add your model for engineering contacts

class AddEngContacts extends Component
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

        $engg = new enggcont_tb();
        $engg->name     = $this->txtename;
        $engg->lastname = $this->txtelname;
        $engg->phone    = $this->txtephone;
        $engg->email    = $this->txteemail;
        $engg->mobile   = $this->txteemob;
        $engg->coustid  = $this->cid;
        $engg->save();

        session()->flash('success', 'Engineering Contact added successfully!');
        $this->reset(); // reset form after success
        return redirect(route('customers.eng.manage'));
    }

    public function render()
    {
        return view('livewire.customers.eng.add-eng-contacts')
            ->layout('layouts.app', ['title' => 'Add Engineering Contact']);
    }
}