<?php

namespace App\Livewire\Customers\Alerts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\alerts_tb;
use Illuminate\Support\Facades\DB;

class ManagePartNumberAlerts extends Component
{
    use WithPagination;

    /* ────── Active filters used in the query ────── */
    public string $searchPart     = '';
    public string $searchCustomer = '';

    /* ────── Text the user types before hitting Search ────── */
    public string $partInput      = '';
    public string $customerInput  = '';

    public int $perPage = 50;
    public int $page    = 1;

    /* Delete‑modal state */
    public bool    $confirmingDelete = false;
    public ?string $delCustomer = null;
    public ?string $delPart     = null;
    public ?string $delRev      = null;

    /* Keep filters in URL */
    protected $queryString = [
        'searchPart'     => ['except' => ''],
        'searchCustomer' => ['except' => ''],
        'page'           => ['except' => 1],
    ];

    /* ────── Validation rules for the input boxes ────── */
    protected array $rules = [
        'partInput'     => 'nullable|string|max:100',
        'customerInput' => 'nullable|string|max:100',
    ];

    /* ────── Search buttons ────── */
    public function search_by_part_number(): void
    {
        $this->validateOnly('partInput');

        $this->searchPart = trim($this->partInput);
        $this->resetPage();
    }

    public function search_by_customer_name(): void
    {
        $this->validateOnly('customerInput');

        $this->searchCustomer = trim($this->customerInput);
        $this->resetPage();
    }

    /* ────── Core grouped query ────── */
    protected function queryAlerts()
    {
        return alerts_tb::query()
            ->select([
                'customer',
                'part_no',
                'rev',
                DB::raw("GROUP_CONCAT(alert ORDER BY id SEPARATOR '\n') AS alerts"),
                DB::raw("MIN(id) AS first_id"),
            ])
            ->where('part_no', '!=', '')
            ->where('atype', 'p')
            ->when($this->searchCustomer,
                fn ($q) => $q->where('customer', 'like', "%{$this->searchCustomer}%"))
            ->when($this->searchPart,
                fn ($q) => $q->where('part_no', 'like', "%{$this->searchPart}%"))
            ->groupBy('customer', 'part_no', 'rev')
            ->orderBy('customer', 'asc')
            ->orderBy('part_no', 'desc')
            ->orderBy('rev', 'desc')
            ->orderByDesc('first_id');
    }

    /* ────── Delete helpers (unchanged) ────── */
    public function confirmDelete(string $customer, string $part, string $rev): void
    {
        $this->delCustomer = $customer;
        $this->delPart     = $part;
        $this->delRev      = $rev;
        $this->confirmingDelete = true;
    }

    public function deleteGroup(): void
    {
        alerts_tb::where('customer', $this->delCustomer)
            ->where('part_no', $this->delPart)
            ->where('rev',     $this->delRev)
            ->delete();

        // $this->resetPage();
        // $this->confirmingDelete = false;
        // $this->delCustomer = $this->delPart = $this->delRev = null;
           // reset pagination
            $this->resetPage();

            // reset all modal-related state
            $this->reset(['confirmingDelete', 'delCustomer', 'delPart', 'delRev']);

        session()->flash('warning', 'Alert group deleted successfully.');
    }

    /* ────── Render ────── */
    public function render()
    {
        $builder = $this->queryAlerts();

        $alerts = DB::table(DB::raw("({$builder->toSql()}) as grouped"))
            ->mergeBindings($builder->getQuery())
            ->paginate($this->perPage)
            ->withQueryString();

        return view('livewire.customers.alerts.manage-part-number-alerts', compact('alerts'))
            ->layout('layouts.app', ['title' => 'Manage Part‑Number Alerts']);
    }
}