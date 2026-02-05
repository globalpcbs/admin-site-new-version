<?php

namespace App\Livewire\Qoutes;

use Livewire\Component;
use App\Models\order_tb as Order;
use App\Models\reminder_tb as Reminder;
use Livewire\WithPagination;

class Manage extends Component
{
    use WithPagination;
    
    public $searchPartNoInput = '';
    public $searchCustomerInput = '';
    public $searchPartNo = '';
    public $searchCustomer = '';

    public $alertMessage = '';
    public $alertType = '';
    protected $listeners = ['alert-hidden' => 'clearAlert'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function searchq()
    {
        $this->searchPartNo = $this->searchPartNoInput;
        $this->reset(['searchPartNoInput']); // Clear the input
        $this->resetPage();
    }

    public function searchbyCustomer()
    {
        $this->searchCustomer = $this->searchCustomerInput;
        $this->reset(['searchCustomerInput']); // Clear the input
        $this->resetPage();
    }

    public function filterclose()
    {
        $this->reset([
            'searchPartNoInput',
            'searchCustomerInput',
            'searchPartNo',
            'searchCustomer'
        ]);
        $this->resetPage();
    }

    public function deleteQuote($id)
    {
        $quote = Order::findOrFail($id);
        $quote->delete();
        Reminder::where('quoteid', $id)->delete();
        $this->alertMessage = 'Quote deleted successfully.';
        $this->alertType = 'warning';
    }

    public function duplicateQuote($id)
    {
        $original = Order::findOrFail($id);
        $newQuote = $original->replicate();
        $newQuote->ord_date = today();
        $newQuote->save();
        $this->alertMessage = 'Quote duplicated successfully.';
        $this->alertType = 'success';
    }

    public function render()
    {
        $quotes = Order::query()
            ->when($this->searchPartNo, fn($q) => $q->where('part_no', 'like', '%' . $this->searchPartNo . '%'))
            ->when($this->searchCustomer, fn($q) => $q->where('cust_name', 'like', '%' . $this->searchCustomer . '%'))
            ->orderBy('ord_id', 'desc')
            ->paginate(100);

        return view('livewire.qoutes.manage', [
            'quotes' => $quotes
        ])->layout('layouts.app', ['title' => 'Manage Quotes']);
    }
}