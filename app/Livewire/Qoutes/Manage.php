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
        $this->matches_partno = []; // Clear dropdown
    }

    public function searchbyCustomer()
    {
        $this->searchCustomer = $this->searchCustomerInput;
        $this->resetPage();
        $this->matches = []; // Clear dropdown
    }

    public function filterclose()
    {
        $this->reset([
            'searchPartNoInput',
            'searchCustomerInput',
            'searchPartNo',
            'searchCustomer',
            'matches',
            'matches_partno'
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
        
        $this->dispatch('refresh-component');
    }

    public function duplicateQuote($id)
    {
        $original = Order::findOrFail($id);
        $newQuote = $original->replicate();
        $newQuote->ord_date = today();
        $newQuote->save();

        $this->alertMessage = 'Quote duplicated successfully.';
        $this->alertType = 'success';
        
        $this->dispatch('refresh-component');
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

    public function onKeyUp(string $value)
    {
        if (mb_strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }
        
        // FIXED: Get unique customer names from order_tb table
        $this->matches = Order::query()
            ->select('cust_name')
            ->where('cust_name', 'like', "%{$value}%")
            ->distinct() // Get unique values only
            ->orderBy('cust_name', 'asc')
            ->get()
            ->toArray();
    }

    public function useMatch($i)
    {
        $this->searchCustomerInput = $this->matches[$i]['cust_name'];
        $this->matches = []; // Clear dropdown immediately
        $this->searchbyCustomer(); // Auto-search after selection
    }

    public function usekeyupno(string $value)
    {
        if (mb_strlen(trim($value)) < 2) {
            $this->matches_partno = [];
            return;
        }
        
        // FIXED: Get unique part numbers from order_tb table
        $this->matches_partno = Order::query()
            ->select('part_no')
            ->where('part_no', 'like', "%{$value}%")
            ->distinct() // Get unique values only
            ->orderBy('part_no', 'asc')
            ->get()
            ->toArray();
    }

    public function useMatchpn($i)
    {
        $this->searchPartNoInput = $this->matches_partno[$i]['part_no'];
        $this->matches_partno = []; // Clear dropdown immediately
        $this->searchq(); // Auto-search after selection
    }
}