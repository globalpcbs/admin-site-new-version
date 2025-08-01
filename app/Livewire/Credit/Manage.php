<?php

namespace App\Livewire\Credit;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use App\Models\credit_tb as Credit;

class Manage extends Component
{
    use WithPagination;

    /* ───── UI state ─────────────────────────── */
    public $perPage       = 50;
    public $sortField     = 'credit_id';
    public $sortDirection = 'desc';

    /* delete-modal state */
    public bool   $confirmingDelete = false;
    public int    $deleteId         = 0;      // credit_id to remove
    public string $delCustomer      = '';
    public string $delPart          = '';
    public string $delRev           = '';
    
    // for search ..
    public $partSearchInput = '';
    public $customerSearchInput = '';
    public $searchPartNo = '';
    public $searchCustomer = '';

    public function mount()
    {
        $this->partSearchInput = $this->searchPartNo;
        $this->customerSearchInput = $this->searchCustomer;
    }

    public function searchByPartNo()
    {
        $this->searchPartNo = $this->partSearchInput;
        $this->resetPage();
    }

    public function searchByCustomer()
    {
        $this->searchCustomer = $this->customerSearchInput;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->searchPartNo = '';
        $this->searchCustomer = '';
        $this->partSearchInput = '';
        $this->customerSearchInput = '';
        $this->resetPage();
    }

    /* ───── Keep state in URL (no $page here) ─ */
    protected $queryString = [
        'searchPartNo'    => ['except' => ''],
        'searchCustomer'  => ['except' => ''],
        'perPage'       => ['except' => 20],
        'sortField'     => ['except' => 'credit_id'],
        'sortDirection' => ['except' => 'desc'],
    ];


    /* sort handler */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    /* ───── Delete flow ─────────────────────── */

    /** user clicked the trash-icon */
    public function confirmDelete(int $id): void
    {
        $credit = Credit::findOrFail($id);

        $this->deleteId    = $id;
        $this->delCustomer = $credit->customer;
        $this->delPart     = $credit->part_no;
        $this->delRev      = $credit->rev;

        $this->confirmingDelete = true;
    }

    /** user clicked “Confirm Delete” */
    public function deleteGroup(): void
    {
        Credit::where('credit_id', $this->deleteId)->delete();

        $this->confirmingDelete = false;
        session()->flash('warning', 'Credit Record deleted.');
        $this->resetPage();            // stay on current list after delete
    }

    public function duplicateRecord($id)
    {
        // Find the original credit
        $original = Credit::with('items')->findOrFail($id);

        // Duplicate the credit (excluding primary key)
        $newCredit = $original->replicate();
        $newCredit->podate = now(); // Optional: reset the date
        $newCredit->save();

        // Duplicate related credit items
        foreach ($original->items as $item) {
            $newItem = $item->replicate();
            $newItem->pid = $newCredit->credit_id;
            $newItem->save();
        }

        session()->flash('success', 'Credit record duplicated successfully.');
        $this->resetPage();
    }

    /* ───── Render ──────────────────────────── */
    public function render()
    {
        \Log::info('Search Part No: ' . $this->searchPartNo);
        \Log::info('Search Customer: ' . $this->searchCustomer);
    
        $credits = Credit::query()
            ->withSum('items as total_price', 'tprice')
            ->when($this->searchPartNo, function (Builder $q) {
                $q->where('part_no', 'like', '%' . $this->searchPartNo . '%');
            })
            ->when($this->searchCustomer, function (Builder $q) {
                $q->where('customer', 'like', '%' . $this->searchCustomer . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    
        return view('livewire.credit.manage', compact('credits'))
            ->layout('layouts.app', ['title' => 'Credits']);
    }
    
}