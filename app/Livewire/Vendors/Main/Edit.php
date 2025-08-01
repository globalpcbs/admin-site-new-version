<?php
namespace App\Livewire\Vendors\Main;

use App\Models\vendor_maincont_tb as VendorMainContact;
use App\Models\vendor_tb as Vendor;
use Livewire\Component;

class Edit extends Component
{
    public $cid;
    public $txtename;
    public $txtelname;
    public $txtephone;
    public $txteemail;
    public $txteemob;

    public $contact_id;
    public $vendors = [];

    public function mount($id)
    {
        $this->contact_id = $id;
        $contact = VendorMainContact::findOrFail($id);

        $this->cid = $contact->coustid;
        $this->txtename = $contact->name;
        $this->txtelname = $contact->lastname;
        $this->txtephone = $contact->phone;
        $this->txteemail = $contact->email;
        $this->txteemob = $contact->mobile;

        $this->vendors = Vendor::orderBy('c_name')->get();
    }

    public function update()
    {
        $this->validate([
            'cid' => 'required|numeric',
            'txtename' => 'required|string|max:255',
            'txtelname' => 'nullable|string|max:255',
            'txtephone' => 'nullable|string|max:20',
            'txteemail' => 'nullable|email|max:255',
            'txteemob' => 'nullable|string|max:20',
        ]);

        $contact = VendorMainContact::findOrFail($this->contact_id);
        $contact->coustid = $this->cid;
        $contact->name = $this->txtename;
        $contact->lastname = $this->txtelname;
        $contact->phone = $this->txtephone;
        $contact->email = $this->txteemail;
        $contact->mobile = $this->txteemob;
        $contact->save();

        session()->flash('success', 'Vendor contact updated successfully!');
        return redirect(route('vendors.main.manage'));
       // return redirect()->route('admin.vendors.main'); // adjust to your route
    }

    public function render()
    {
        return view('livewire.vendors.main.edit')
            ->layout('layouts.app', ['title' => 'Edit Vendor Main Contact']);
    }
}