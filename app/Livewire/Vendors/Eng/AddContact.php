<?php

namespace App\Livewire\Vendors\Eng;

use App\Models\vendor_enggcont_tb;
use App\Models\vendor_tb;
use Livewire\Component;
use DB;

class AddContact extends Component
{
    public $name, $lastname, $phone, $email, $mobile, $coustid;

    public $vendors = [];

    public function mount()
    {
        $this->vendors = vendor_tb::orderBy('c_name')->get();
    }

    public function save()
    {
         $validated = $this->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:50',
            'coustid' => 'required|exists:vendor_tb,data_id',
        ]);

        DB::table('vendor_enggcont_tb')->insert($validated);

        session()->flash('success', 'Vendor engineering contact added successfully.');
        return redirect(route('vendors.eng.manage'));
       // return redirect()->route('vendor.enggcontact.index'); // Change this route as needed
    }

    public function render()
    {
        return view('livewire.vendors.eng.add-contact')
            ->layout('layouts.app', ['title' => 'Add Vendor Engineering Contact']);
    }
}