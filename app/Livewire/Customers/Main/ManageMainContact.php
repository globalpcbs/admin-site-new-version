<?php

namespace App\Livewire\Customers\Main;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\maincont_tb as MainContact;
use App\Models\data_tb as Customer;

class ManageMainContact extends Component
{
    use WithPagination;

    public $searchCustomer = '';

    public function filterCustomers($customerId)
    {
        $this->searchCustomer = $customerId;
        $this->resetPage();
    }

    public function updatingSearchCustomer()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Get distinct customer names from data_tb
        $customers = Customer::select('data_id', 'c_name')->distinct()->orderby('c_name','asc')->get();

        // Get contacts with optional filtering
        $contacts = MainContact::when($this->searchCustomer, function ($query) {
            $query->where('coustid', $this->searchCustomer);
        })
        ->orderBy('enggcont_id', 'desc')
        ->paginate(20);

        return view('livewire.customers.main.manage-main-contact', [
            'contacts' => $contacts,
            'customers' => $customers,
        ])->layout('layouts.app', ['title' => 'Customers Main Manage']);
    }
    public $confirmingDelete = false;
    public $contactToDeleteId = null;

    public function confirmDelete($id)
    {
        $this->contactToDeleteId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteCustomer()
    {
        if ($this->contactToDeleteId) {
           // dd($this->contactToDeleteId);
            MainContact::find($this->contactToDeleteId)?->delete();
            session()->flash('warning','Customer Main Contact Deleted Succesfully');
           // $this->dispatch('notify', 'Contact deleted successfully!');
        }

        $this->confirmingDelete = false;
        $this->contactToDeleteId = null;
    }
}
