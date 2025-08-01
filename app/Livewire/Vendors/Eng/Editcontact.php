<?php

namespace App\Livewire\Vendors\Eng;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Editcontact extends Component
{
    public $contactId;
    public $name, $lastname, $phone, $email, $mobile, $coustid;
    public $vendors = [];

    public function mount($id)
    {
        $this->contactId = $id;
        $contact = DB::table('vendor_enggcont_tb')->where('enggcont_id', $id)->first();
       // dd($contact);
        if (!$contact) {
            abort(404);
        }

        $this->name = $contact->name;
        $this->lastname = $contact->lastname;
        $this->phone = $contact->phone;
        $this->email = $contact->email;
        $this->mobile = $contact->mobile;
        $this->coustid = $contact->coustid;

        $this->vendors = DB::table('vendor_tb')->orderBy('c_name')->get();
    }

    public function update()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:50',
            'coustid' => 'required|exists:vendor_tb,data_id',
        ]);

        DB::table('vendor_enggcont_tb')->where('enggcont_id', $this->contactId)->update($validated);

        session()->flash('success', 'Contact updated successfully.');
        return redirect(route('vendors.eng.manage'));
        // redirect()->route('vendor.enggcontact.index');
    }

    public function render()
    {
        return view('livewire.vendors.eng.editcontact')
            ->layout('layouts.app', ['title' => 'Edit Vendor Eng Contact']);
    }
}