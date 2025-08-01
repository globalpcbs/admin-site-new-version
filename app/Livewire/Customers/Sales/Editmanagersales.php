<?php

namespace App\Livewire\Customers\Sales;

use Livewire\Component;
use App\Models\rep_tb;
use App\Models\data_tb;

class Editmanagersales extends Component
{
    /* ---------- Form fields (same names as Add) ---------- */
    public $repname, $compname, $txtemail, $txtaddress, $txtaddress2, $txtaddress3;
    public $txtphone2, $txtfax2, $txtweb, $txtepay, $txtecomments;
    public $indirect = false;
    public $invsoldto = [];
    public $comval;

    public rep_tb $rep;   // the model being edited

    /* ---------- Validation rules ---------- */
    protected $rules = [
        'repname'       => 'required|string|max:255',
        'compname'      => 'nullable|string|max:255',
        'txtemail'      => 'nullable|email|max:255',
        'txtaddress'    => 'nullable|string|max:255',
        'txtaddress2'   => 'nullable|string|max:255',
        'txtaddress3'   => 'nullable|string|max:255',
        'txtphone2'     => 'nullable|string|max:50',
        'txtfax2'       => 'nullable|string|max:50',
        'txtweb'        => 'nullable|string|max:255',
        'txtepay'       => 'nullable|string|max:255',
        'txtecomments'  => 'nullable|string',
        'comval'        => 'nullable|numeric',
        'invsoldto'     => 'array',
    ];

    /* ---------- Mount: load row ---------- */
    public function mount(int $id): void
    {
        $this->rep = rep_tb::findOrFail($id);

        // populate fields from DB
        $this->repname     = $this->rep->r_name;
        $this->compname    = $this->rep->c_name;
        $this->txtemail    = $this->rep->c_email;
        $this->txtaddress  = $this->rep->c_address;
        $this->txtaddress2 = $this->rep->c_address2;
        $this->txtaddress3 = $this->rep->c_address3;
        $this->txtphone2   = $this->rep->c_phone;
        $this->txtfax2     = $this->rep->c_fax;
        $this->txtweb      = $this->rep->c_website;
        $this->txtepay     = $this->rep->e_payment;
        $this->txtecomments= $this->rep->e_comments;
        $this->indirect    = $this->rep->indirect == 1;
        $this->comval      = $this->rep->comval;

        // stored as |id|id|…|   → turn into array
        $this->invsoldto = array_filter(
            explode('|', $this->rep->invsoldto),
            fn ($v) => $v !== ''
        );
    }

    /* ---------- Save (update) ---------- */
    public function save()
    {
        $this->validate();

        $this->rep->r_name     = $this->repname;
        $this->rep->c_name     = $this->compname;
        $this->rep->c_email    = $this->txtemail;
        $this->rep->c_address  = $this->txtaddress;
        $this->rep->c_address2 = $this->txtaddress2;
        $this->rep->c_address3 = $this->txtaddress3;
        $this->rep->c_phone    = $this->txtphone2;
        $this->rep->c_fax      = $this->txtfax2;
        $this->rep->c_website  = $this->txtweb;
        $this->rep->e_payment  = $this->txtepay;
        $this->rep->e_comments = $this->txtecomments;
        $this->rep->indirect   = $this->indirect ? 1 : 0;
        $this->rep->invsoldto  = '|' . implode('|', $this->invsoldto) . '|';
        $this->rep->comval     = $this->comval;

        $this->rep->save();

        session()->flash('success', 'Sales rep updated successfully.');
        return redirect(route('customers.sales.manage-rep'));
        // redirect back to list if you have one
        // return redirect()->route('sales.manage');
    }

    /* ---------- Render ---------- */
    public function render()
    {
        return view('livewire.customers.sales.editmanagersales', [
            'customers' => data_tb::orderBy('c_name')->get(),
        ])->layout('layouts.app', ['title' => 'Edit Sales Rep']);
    }
}