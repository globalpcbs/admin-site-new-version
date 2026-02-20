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
    public $alertMessage;
    public $alertType;

    /* Keep filters in URL */
    protected $queryString = [
        'searchPartNo'     => ['except' => ''],
        'searchCustomer' => ['except' => ''],
        'page'           => ['except' => 1],
    ];

    protected function getListeners()
    {
        return [
            'alert-hidden' => 'clearAlert',
        ];
    }

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    /* ────── Search methods ────── */
    public function searchq(): void
    {
        // Reset customer filter and clear its input
        $this->searchCustomer = '';
        $this->searchCustomerInput = '';
        $this->matches_customer = [];
        $this->dispatch('clear-customer-search'); // Clear Alpine customer field

        $this->searchPartNo = $this->searchPartNoInput;
        $this->resetPage();
        $this->matches_partno = [];
    }

    public function searchbyCustomer(): void
    {
        // Reset part number filter and clear its input
        $this->searchPartNo = '';
        $this->searchPartNoInput = '';
        $this->matches_partno = [];
        $this->dispatch('clear-part-search'); // Clear Alpine part field

        $this->searchCustomer = $this->searchCustomerInput;
        $this->resetPage();
        $this->matches_customer = [];
    }

    /* ────── Enter key handlers (kept for reference, but Alpine handles Enter now) ────── */
    public function onPartInputEnter(): void
    {
        $this->resetPage();
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
        $this->matches_partno = [];
        $this->searchq();
    }

    public function useMatch($index): void
    {
        $this->searchCustomerInput = $this->matches_customer[$index]['customer'];
        $this->matches_customer = [];
        $this->searchbyCustomer();
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
        // Also clear Alpine fields
        $this->dispatch('clear-part-search');
        $this->dispatch('clear-customer-search');
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

    /* ────── Delete method ────── */
    public function deleteGroup(string $customer, string $part, string $rev): void
    {
        alerts_tb::where('customer', $customer)
            ->where('part_no', $part)
            ->where('rev', $rev)
            ->delete();

        $this->resetPage();

        $this->alertMessage = 'Part Number Alert deleted successfully.';
        $this->alertType = 'danger';
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