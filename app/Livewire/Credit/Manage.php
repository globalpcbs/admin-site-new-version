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
    
    // Alpine.js compatible filter properties
    public $searchPartNoInput = '';
    public $searchCustomerInput = '';
    public $searchPartNo = '';
    public $searchCustomer = '';

    // SIMPLE alert properties
    public $alertMessage = '';
    public $alertType = '';
    protected $listeners = ['alert-hidden' => 'clearAlert'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function mount()
    {
        $this->searchPartNoInput = $this->searchPartNo;
        $this->searchCustomerInput = $this->searchCustomer;
    }

    // Alpine.js compatible search methods
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

    /** user clicked "Confirm Delete" */
    public function deleteGroup($id): void
    {
        $this->deleteId = $id;
        Credit::where('credit_id', $this->deleteId)->delete();

        $this->confirmingDelete = false;
        
        $this->alertMessage = 'Credit deleted successfully.';
        $this->alertType = 'danger';
        
        $this->dispatch('refresh-component');
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

        $this->alertMessage = 'Credit record duplicated successfully.';
        $this->alertType = 'success';
        
        $this->dispatch('refresh-component');
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