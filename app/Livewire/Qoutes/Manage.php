<?php

namespace App\Livewire\Qoutes;

use Livewire\Component;
use App\Models\order_tb as Order;
use App\Models\reminder_tb as Reminder;
use Livewire\WithPagination;

class Manage extends Component
{
    use WithPagination;

    public $searchPartNo = '';
    public $searchCustomer = '';

    public function updatingSearchPartNo()
    {
        $this->resetPage();
    }

    public function updatingSearchCustomer()
    {
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
}