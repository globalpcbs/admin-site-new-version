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
        // for search ...
    public $searchPartNoInput = '';
    public $matches    = [];          // array of suggestions ⬅️  NEW
    public $matches_partno = []; // array of part no ..
    public $searchCustomerInput = '';
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
        $this->partSearchInput = $this->searchPartNo;
        $this->customerSearchInput = $this->searchCustomer;
    }

    public function searchByPartNo()
    {
        $this->searchPartNo = $this->partSearchInput;
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
    public function deleteGroup($id): void
    {
        $this->deleteId = $id;
        Credit::where('credit_id', $this->deleteId)->delete();

        $this->confirmingDelete = false;
        // SIMPLE: Just set the alert
        $this->alertMessage = 'Credit deleted successfully.';
        $this->alertType = 'danger';
        
        // Clear alert after a short delay by forcing a re-render
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

        // session()->flash('success', 'Credit record duplicated successfully.');
        // $this->resetPage();
        $this->alertMessage = 'Credit record duplicated successfully.';
        $this->alertType = 'success';
        
        // Clear alert after a short delay by forcing a re-render
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
        // search ...
    public function searchq(){
         // assign the input values to the actual search vars
        $this->searchPartNo = $this->searchPartNoInput;
       // dd($this->partSearchInput);
        //  dd($this->searchPartNo);
            // reset pagination
        $this->resetPage();

        // clear the input fields (but keep actual filters intact)
       $this->reset(['searchPartNoInput']);  
    }
    public function searchbyCustomer() {
       // $customer = data_tb::where('c_name',$this->searchCustomerInput)->first();
       // dd($customer->data_id);
        $this->searchCustomer = $this->searchCustomerInput;
       // reset pagination
       $this->resetPage();

        // clear the input fields (but keep actual filters intact)
       $this->reset(['searchCustomerInput']);    
    }
        // search ...
    public function onKeyUp(string $value){
       // dd($value);
         if (mb_strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }
        $this->matches = Credit::query()
            ->where('customer', 'like', "%{$value}%")
            ->get()
            ->toArray();
        //dd($this->matches);
    }   
    public function useMatch($i){
       // dd($this->matches[$i]['data_id']);
        $this->searchCustomerInput = $this->matches[$i]['customer'];
        $this->matches = [];
    }
    public function usekeyupno(string $value){
         if (mb_strlen(trim($value)) < 2) {
            $this->matches_partno = [];
            return;
        }
        $this->matches_partno = Credit::query()
        ->select('part_no')
        ->where('part_no', 'like', "%{$value}%")
        ->get()
        ->toArray();
    }
    public function useMatchpn($i){
        $this->searchPartNoInput = $this->matches_partno[$i]['part_no'];
        $this->matches_partno = [];
    }
        public function resetFilters()
    {
        $this->reset(['searchPartNo', 'searchCustomer']);
    }
    
}