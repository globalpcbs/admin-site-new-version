<?php

namespace App\Livewire\Qoutes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\order_tb as Order;
use App\Models\reminder_tb as Reminder;

class Reminders extends Component
{
    use WithPagination;

    public $searchBy = 'quote';
    public $searchTerm = '';
    public $searchPart = '';
    public $searchCustomer = '';
    public $perPage = 100;

    protected $queryString = [
        'searchBy' => ['except' => 'quote'],
        'searchTerm' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function search($type)
    {
        $this->searchBy = $type;
        $this->searchTerm = $type === 'part' ? $this->searchPart : $this->searchCustomer;
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->searchBy = 'quote';
        $this->searchTerm = '';
        $this->searchPart = '';
        $this->searchCustomer = '';
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->enabled = $reminder->enabled === 'yes' ? 'no' : 'yes';
         session()->flash('success', 'Reminder Status Is Updated successfully!');
        $reminder->save();
    }

    public function deleteReminder($id)
    {
         try {
            Reminder::findOrFail($id)->delete();
            session()->flash('warning', 'Reminder deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('warning', 'Error deleting reminder: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Reminder::with('order')
            ->select('reminder_tb.*')
            ->join('order_tb', 'reminder_tb.quoteid', '=', 'order_tb.ord_id')
            ->orderBy('reminder_tb.quoteid', 'desc');

        if ($this->searchTerm) {
            if ($this->searchBy === 'customer') {
                $query->where('order_tb.cust_name', 'like', '%'.$this->searchTerm.'%');
            } elseif ($this->searchBy === 'part') {
                $parts = explode('_', $this->searchTerm);
                $query->where('order_tb.part_no', $parts[0]);
            }
        }

        $reminders = $query->paginate($this->perPage);

        return view('livewire.qoutes.reminders', [
            'reminders' => $reminders
        ])->layout('layouts.app', ['title' => 'Reminder Quotes']);
    }
}