<?php

namespace App\Livewire\Customers\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\rep_tb;

class ManagSalesRep extends Component
{
    use WithPagination;

    // if you use Bootstrap for pagination links
    protected $paginationTheme = 'bootstrap';

    // keep the filter in the URL so a refresh keeps the same view
    protected $queryString = ['search' => ['except' => '']];

    public $search = '';
    public $confirmingDelete = false;
    public $deleteId;

    /**
     * Whenever *any* public property updates, check if it was “search” and
     * reset pagination.  (Covers both .defer and .lazy cases.)
     */
    public function updated($property, $value)
    {
        if ($property === 'search') {
            $this->resetPage();         // page → 1
        }
    }

    /* ---------------- Delete helpers ---------------- */
    public function deleteConfirm($id)
    {
        $this->deleteId         = $id;
        $this->confirmingDelete = true;
    }

    public function deleteCustomer()
    {
        rep_tb::where('id', $this->deleteId)->delete();

        session()->flash('message', 'Sales Rep deleted successfully.');

        $this->confirmingDelete = false;
        $this->deleteId         = null;
        $this->resetPage();
    }

    /* ---------------- Render ---------------- */
    public function render()
    {
        $repsQuery = rep_tb::query();

        // If a rep is selected, narrow the query
        if ($this->search !== '') {
            $repsQuery->where('repid', $this->search);   // no cast → works for INT *or* string keys
        }

        return view('livewire.customers.sales.manag-sales-rep', [
            'reps'    => $repsQuery->orderBy('repid', 'desc')->paginate(10),
            'allReps' => rep_tb::select('repid', 'r_name')->orderBy('r_name')->get(),
        ])->layout('layouts.app', [
            'title' => 'Manage Sales Reps',
        ]);
    }
}