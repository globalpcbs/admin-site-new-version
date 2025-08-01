<?php

namespace App\Livewire\Customers\Eng;

use App\Models\data_tb as Customer;
use App\Models\enggcont_tb as EnggContact;
use Livewire\Component;
use Livewire\WithPagination;

class ManageEngContacts extends Component
{
    use WithPagination;

    public $customer_id;
    public $confirmingDelete = false;
    public $contactToDelete = null;

    protected $paginationTheme = 'bootstrap';

    public function filterCustomers($id)
    {
        $this->customer_id = $id;
        //dd($this->customer_id);
        $this->resetPage();
    }

       public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->contactToDelete = $id;
    }

    public function deleteCustomer()
    {
        if ($this->contactToDelete) {
            EnggContact::where('enggcont_id', $this->contactToDelete)->delete();
            session()->flash('warning', 'Contact deleted successfully.');
        }

        $this->confirmingDelete = false;
        $this->contactToDelete = null;
    }

    
    public function render()
    {
        $contacts = EnggContact::when($this->customer_id, function ($query) {
                $query->where('coustid', $this->customer_id);
            })
            ->with('customer')
            ->orderByDesc('enggcont_id')
            ->paginate(10);

        $customers = Customer::orderBy('c_name')->get();

        return view('livewire.customers.eng.manage-eng-contacts', [
            'contacts' => $contacts,
            'customers' => $customers
        ])->layout('layouts.app', ['title' => 'Manage Engineering Contact']);
    }

    public function delete($id)
    {
        EnggContact::where('enggcont_id', $id)->delete();
        session()->flash('warning', 'Contact deleted successfully.');
    }
}
