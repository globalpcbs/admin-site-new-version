<?php

namespace App\Livewire\Qoutes;

use Livewire\Component;
use App\Models\order_tb as Order;
use App\Models\reminder_tb as Reminder;
use App\Models\data_tb as customer;
use Livewire\WithPagination;

class Manage extends Component
{
    use WithPagination;
    
    public $searchPartNoInput = '';
    public $searchCustomerInput = '';
    public $searchPartNo = '';
    public $searchCustomer = '';
    
    // search match
    public $search = '';
    public $matches = [];
    public $matches_partno = [];

    // SIMPLE alert properties
    public $alertMessage = '';
    public $alertType = '';
    protected $listeners = ['alert-hidden' => 'clearAlert'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function updatingSearchPartNo()
    {
        $this->resetPage();
    }

    public function updatingSearchCustomer()
    {
        $this->resetPage();
    }

    public function searchq()
    {
        $this->searchPartNo = $this->searchPartNoInput;
        $this->resetPage();
        $this->reset(['searchPartNoInput']);
    }

    public function searchbyCustomer()
    {
        $this->searchCustomer = $this->searchCustomerInput;
        $this->resetPage();
        $this->reset(['searchCustomerInput']);
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

        // SIMPLE: Just set the alert
        $this->alertMessage = 'Quote deleted successfully.';
        $this->alertType = 'warning';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
    }

    public function duplicateQuote($id)
    {
        $original = Order::findOrFail($id);
        $newQuote = $original->replicate();
        $newQuote->ord_date = today();
        $newQuote->save();

        // SIMPLE: Just set the alert
        $this->alertMessage = 'Quote duplicated successfully.';
        $this->alertType = 'success';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
    }

    // Listen for refresh event

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

    public function onKeyUp(string $value)
    {
        if (mb_strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }
        $this->matches = customer::query()
            ->select('c_name')
            ->where('c_name', 'like', "%{$value}%")
            ->get()
            ->toArray();
    }

    public function useMatch($i)
    {
        $this->searchCustomerInput = $this->matches[$i]['c_name'];
        $this->matches = [];
    }

    public function usekeyupno(string $value)
    {
        if (mb_strlen(trim($value)) < 2) {
            $this->matches_partno = [];
            return;
        }
        $this->matches_partno = Order::query()
            ->select('part_no')
            ->where('part_no', 'like', "%{$value}%")
            ->get()
            ->toArray();
    }

    public function useMatchpn($i)
    {
        $this->searchPartNoInput = $this->matches_partno[$i]['part_no'];
        $this->matches_partno = [];
    }
}