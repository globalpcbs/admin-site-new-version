<?php 
namespace App\Livewire\Shippers;

use Livewire\Component;
use App\Models\shipper_tb as Shipper;

class Edit extends Component
{
    public $shipperId;

    // All shipper fields
    public $c_name, $c_address, $c_address2, $c_address3, $c_phone, $c_fax, $c_website, $c_bcontact;
    public $e_name, $e_lname, $e_phone, $e_email, $e_payment, $e_comments, $e_other;

    public function mount($id)
    {
        $this->shipperId = $id;
        $shipper = Shipper::where('data_id',$this->shipperId)->first();
       // dd($shipper);
        $this->fill($shipper->toArray());
    }

    public function updateShipper()
    {
        $this->validate([
            'c_name' => 'required|string|max:255',
            'e_name' => 'required|string|max:255',
            // Add more validation rules
        ]);

        $shipper = Shipper::where('data_id',$this->shipperId)->first();
        $shipper->fill([
            'c_name' => $this->c_name,
            'c_address' => $this->c_address,
            'c_address2' => $this->c_address2,
            'c_address3' => $this->c_address3,
            'c_phone' => $this->c_phone,
            'c_fax' => $this->c_fax,
            'c_website' => $this->c_website,
            'c_bcontact' => $this->c_bcontact,
            'e_name' => $this->e_name,
            'e_lname' => $this->e_lname,
            'e_phone' => $this->e_phone,
            'e_email' => $this->e_email,
            'e_payment' => $this->e_payment,
            'e_comments' => $this->e_comments,
            'e_other' => $this->e_other,
        ]);
        $shipper->save();

        session()->flash('success', 'Shipper updated successfully.');
        $this->dispatch('closeEditForm');
        return redirect(route('shippers.manage'));
    }

    public function render()
    {
        return view('livewire.shippers.edit')->layout('layouts.app', ['title' => 'Edit Shipper']);;
    }
}