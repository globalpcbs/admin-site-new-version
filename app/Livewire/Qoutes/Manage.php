<?php

namespace App\Livewire\Qoutes;

use Livewire\Component;
use App\Models\order_tb as Order;
use App\Models\reminder_tb as Reminder;
use App\Models\data_tb as customer;
//use App\Models\
use Livewire\WithPagination;

class Manage extends Component
{
    use WithPagination;
    public $searchPartNoInput = '';
    public $searchCustomerInput = '';
    public $searchPartNo = '';
    public $searchCustomer = '';
    // search match .. 
    public $search     = '';          // what the user is typing
    public $matches    = [];          // array of suggestions ⬅️  NEW
    public $matches_partno = []; // array of part no ..

    public function updatingSearchPartNo()
    {
        $this->resetPage();
    }

    public function updatingSearchCustomer()
    {
        $this->resetPage();
    }


    public function searchq(){
         // assign the input values to the actual search vars
        $this->searchPartNo = $this->searchPartNoInput;
      //  dd($this->searchPartNo);
        // reset pagination
       $this->resetPage();

        // clear the input fields (but keep actual filters intact)
       $this->reset(['searchPartNoInput']);    
    }

    public function searchbyCustomer() {
       // dd($this->searchCustomerInput);
        $this->searchCustomer = $this->searchCustomerInput;
       // reset pagination
       $this->resetPage();

        // clear the input fields (but keep actual filters intact)
       $this->reset(['searchCustomerInput']);    
    }
    // filterclose
    public function filterclose(){
       // reset filters + inputs
        $this->reset([
            'searchPartNoInput',
            'searchCustomerInput',
            'searchPartNo',
            'searchCustomer'
        ]);

        // reset pagination back to first page
        $this->resetPage();
    }
    public function deleteQuote($id)
    {
        $quote = Order::findOrFail($id);
        $quote->delete();

        Reminder::where('quoteid', $id)->delete();

        session()->flash('warning', 'Quote deleted successfully.');
    }

    public function duplicateQuote($id)
    {
        $original = Order::findOrFail($id);
       $newQuote = $original->replicate();
$newQuote->ord_date = today(); // sets only the date, no time
$newQuote->save();

        session()->flash('success', 'Quote duplicated successfully.');
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
    // search ...
    public function onKeyUp(string $value){
       // dd($value);
         if (mb_strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }
        $this->matches = customer::query()
            ->select('c_name')
            ->where('c_name', 'like', "%{$value}%")
            ->get()
            ->toArray();
        //dd($this->matches);
    }   
    public function useMatch($i){
        $this->searchCustomerInput = $this->matches[$i]['c_name'];
        $this->matches = [];
    }
    public function usekeyupno(string $value){
         if (mb_strlen(trim($value)) < 2) {
            $this->matches_partno = [];
            return;
        }
        $this->matches_partno = order::query()
        ->select('part_no')
        ->where('part_no', 'like', "%{$value}%")
        ->get()
        ->toArray();
    }
    public function useMatchpn($i){
        $this->searchPartNoInput = $this->matches_partno[$i]['part_no'];
        $this->matches_partno = [];
    }
}