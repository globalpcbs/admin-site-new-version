<?php

namespace App\Livewire\Customers\Alerts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\alerts_tb;
use Illuminate\Support\Facades\DB;

class ManagePartNumberAlerts extends Component
{
    use WithPagination;

    /* ────── Search inputs ────── */
    public string $searchPartNoInput = '';
    public string $searchCustomerInput = '';
    public string $searchPartNo = '';
    public string $searchCustomer = '';
    
    /* ────── Dropdown suggestions ────── */
    public array $matches_partno = [];
    public array $matches_customer = [];

    public int $perPage = 50;
    public int $page    = 1;

    /* Delete‑modal state */
    public bool    $confirmingDelete = false;
    public ?string $delCustomer = null;
    public ?string $delPart     = null;
    public ?string $delRev      = null;

    /* Keep filters in URL */
    protected $queryString = [
        'searchPartNo'     => ['except' => ''],
        'searchCustomer' => ['except' => ''],
        'page'           => ['except' => 1],
    ];

    /* ────── Search methods ────── */
    public function searchq(): void
    {
        $this->searchPartNo = $this->searchPartNoInput;
        $this->resetPage();
        $this->matches_partno = []; // Clear dropdown
    }

    public function searchbyCustomer(): void
    {
        $this->searchCustomer = $this->searchCustomerInput;
        $this->resetPage();
        $this->matches_customer = []; // Clear dropdown
    }

    /* ────── Enter key handlers ────── */
    public function onPartInputEnter(): void
    {
        $this->searchq();
    }

    public function onCustomerInputEnter(): void
    {
        $this->searchbyCustomer();
    }

    /* ────── Auto-complete methods ────── */
    public function usekeyupno(string $value): void
    {
        if (mb_strlen(trim($value)) < 2) {
            $this->matches_partno = [];
            return;
        }
        
        $this->matches_partno = alerts_tb::query()
            ->select('part_no')
            ->where('part_no', '!=', '')
            ->where('part_no', 'like', "%{$value}%")
            ->where('atype', 'p')
            ->distinct()
            ->orderBy('part_no', 'asc')
            ->get()
            ->toArray();
    }

    public function onKeyUp(string $value): void
    {
        if (mb_strlen(trim($value)) < 2) {
            $this->matches_customer = [];
            return;
        }
        
        $this->matches_customer = alerts_tb::query()
            ->select('customer')
            ->where('customer', '!=', '')
            ->where('customer', 'like', "%{$value}%")
            ->where('atype', 'p')
            ->distinct()
            ->orderBy('customer', 'asc')
            ->get()
            ->toArray();
    }

    /* ────── Select from dropdown ────── */
    public function useMatchpn($index): void
    {
        $this->searchPartNoInput = $this->matches_partno[$index]['part_no'];
        $this->matches_partno = []; // Clear dropdown immediately
        $this->searchq(); // Auto-search after selection
    }

    public function useMatch($index): void
    {
        $this->searchCustomerInput = $this->matches_customer[$index]['customer'];
        $this->matches_customer = []; // Clear dropdown immediately
        $this->searchbyCustomer(); // Auto-search after selection
    }

    /* ────── Clear filters ────── */
    public function filterclose(): void
    {
        $this->reset([
            'searchPartNoInput',
            'searchCustomerInput',
            'searchPartNo',
            'searchCustomer',
            'matches_partno',
            'matches_customer'
        ]);
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
            ->when($this->searchPartNo,
                fn ($q) => $q->where('part_no', 'like', "%{$this->searchPartNo}%"))
            ->groupBy('customer', 'part_no', 'rev')
            ->orderByRaw('LOWER(customer) ASC')
            ->orderBy('part_no', 'desc')
            ->orderBy('rev', 'desc')
            ->orderByDesc('first_id');
    }

    /* ────── Delete helpers ────── */
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

        $this->resetPage();
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