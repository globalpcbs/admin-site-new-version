<?php 
namespace App\Livewire\Customers\Sales;

use Livewire\Component;
use App\Models\rep_tb;
use App\Models\data_tb;

class Add extends Component
{
    public $repname, $compname, $txtemail, $txtaddress, $txtaddress2, $txtaddress3;
    public $txtphone2, $txtfax2, $txtweb, $txtepay, $txtecomments;
    public $indirect = false;
    public $invsoldto = [];
    public $comval;

    protected $rules = [
        'repname' => 'required|string|max:255',
        'compname' => 'nullable|string|max:255',
        'txtemail' => 'nullable|email|max:255',
        'txtaddress' => 'nullable|string|max:255',
        'txtaddress2' => 'nullable|string|max:255',
        'txtaddress3' => 'nullable|string|max:255',
        'txtphone2' => 'nullable|string|max:50',
        'txtfax2' => 'nullable|string|max:50',
        'txtweb' => 'nullable|string|max:255',
        'txtepay' => 'nullable|string|max:255',
        'txtecomments' => 'nullable|string',
        'comval' => 'nullable|numeric',
        'invsoldto' => 'array',
    ];

    public function save()
    {
        $this->validate();

        $rep = new rep_tb();
        $rep->r_name = $this->repname;
        $rep->c_name = $this->compname;
        $rep->c_email = $this->txtemail;
        $rep->c_address = $this->txtaddress;
        $rep->c_address2 = $this->txtaddress2;
        $rep->c_address3 = $this->txtaddress3;
        $rep->c_phone = $this->txtphone2;
        $rep->c_fax = $this->txtfax2;
        $rep->c_website = $this->txtweb;
        $rep->e_payment = $this->txtepay;
        $rep->e_comments = $this->txtecomments;
        $rep->indirect = $this->indirect ? 1 : 0;
        $rep->invsoldto = '|' . implode('|', $this->invsoldto) . '|';
        $rep->comval = $this->comval;

        $rep->save();

        session()->flash('success', 'Sales rep added successfully.');
        return redirect(route('customers.sales.manage-rep'));

        //return redirect()->to('/manage-sales-rep'); // Replace with your actual route
    }

    public function render()
    {
        return view('livewire.customers.sales.add', [
            'customers' => data_tb::orderBy('c_name')->get()
        ])->layout('layouts.app', ['title' => 'Add Sales Repo']);
    }
}