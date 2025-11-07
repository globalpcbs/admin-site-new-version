<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\data_tb as Customer;

class ManageCustomers extends Component
{
    use WithPagination;

    public $search = '';
    public $confirmingDelete = false;
    public $deleteCustomerId = null;
     // SIMPLE alert properties
    public $alertMessage = '';
    public $alertType = '';
    protected $listeners = ['alert-hidden' => 'clearAlert'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function filterCustomers($value)
    {
        $this->search = $value;
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deleteCustomerId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteCustomer($id)
    {
        $this->deleteCustomerId = $id;
        Customer::where('data_id', $this->deleteCustomerId)->delete();
       // SIMPLE: Just set the alert
        $this->alertMessage = 'Customer deleted successfully.';
        $this->alertType = 'danger';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch(event: 'refresh-component');
    }

    public function render()
    {
        $allCustomers = Customer::select('c_name')->orderby('c_name')->distinct()->orderby('c_name','asc')->get();

        $customers = Customer::when($this->search, function ($query) {
            $query->where('c_name', $this->search);
        })->orderby('data_id','desc')->paginate(20);

        return view('livewire.customers.manage-customers', [
            'customers' => $customers,
            'allCustomers' => $allCustomers
        ])->layout('layouts.app', ['title' => 'Manage Customers']);
    }
}
