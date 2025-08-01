<?php 
namespace App\Livewire\Vendors;

use App\Models\vendor_tb as Vendor;
use Livewire\Component;

class Edit extends Component
{
    public $vendor_id;
    public $c_name, $c_shortname, $c_address, $c_address2, $c_address3;
    public $c_phone, $c_fax, $c_website, $e_payment, $e_comments, $e_other;

    public function mount($id)
    {
        $vendor = Vendor::where('data_id', $id)->firstOrFail();

        $this->vendor_id   = $vendor->data_id;
        $this->c_name      = $vendor->c_name;
        $this->c_shortname = $vendor->c_shortname;
        $this->c_address   = $vendor->c_address;
        $this->c_address2  = $vendor->c_address2;
        $this->c_address3  = $vendor->c_address3;
        $this->c_phone     = $vendor->c_phone;
        $this->c_fax       = $vendor->c_fax;
        $this->c_website   = $vendor->c_website;
        $this->e_payment   = $vendor->e_payment;
        $this->e_comments  = $vendor->e_comments;
        $this->e_other     = $vendor->e_other;
    }

    public function submit()
    {
        $this->validate([
            'c_name'      => 'required',
            'c_shortname' => 'required',
            'c_phone'     => 'required',
            'e_payment'   => 'required',
        ]);

        $vendor = Vendor::where('data_id', $this->vendor_id)->firstOrFail();

        $vendor->update([
            'c_name'      => $this->c_name,
            'c_shortname' => $this->c_shortname,
            'c_address'   => $this->c_address,
            'c_address2'  => $this->c_address2,
            'c_address3'  => $this->c_address3,
            'c_phone'     => $this->c_phone,
            'c_fax'       => $this->c_fax,
            'c_website'   => $this->c_website,
            'e_payment'   => $this->e_payment,
            'e_comments'  => $this->e_comments,
            'e_other'     => $this->e_other,
        ]);

        session()->flash('success', 'Vendor updated successfully.');
        return redirect(route('manage.vendor'));
    }

    public function render()
    {
        return view('livewire.vendors.edit')->layout('layouts.app', ['title' => 'Edit Vendor']);
    }
}