<?php

namespace App\Livewire\Customers\Alerts;

use Livewire\Component;
use App\Models\alerts_tb;
use App\Models\order_tb;
use Illuminate\Support\Facades\DB;

class Editpartnumber extends Component
{
    /* ───────── Route‑bound params ───────── */
    public string $customer;
    public string $part;
    public string $rev = '';

    /* ───────── Form fields ───────── */
    public string $txtcust = '';
    public string $pno     = '';
    public string $rev_inp = '';
    public array  $alerts  = [];

    /* ───────── Mount: preload existing data ───────── */
    public function mount(string $customer, string $part, string $rev = ''): void
    {
        $this->customer = $this->txtcust = trim($customer);
        $this->part     = $this->pno     = trim($part);
        $this->rev      = $this->rev_inp = trim($rev);

        $this->loadAlertsForPart();
    }

    /**
     * Load alerts for the current part
     */
    public function loadAlertsForPart(): void
    {
        // fetch current alerts for this trio
        $rows = alerts_tb::whereRaw('TRIM(customer)=?', [$this->txtcust])
            ->whereRaw('TRIM(part_no)=?', [$this->pno])
            ->whereRaw('TRIM(rev)=?', [$this->rev_inp])
            ->where('atype', 'p')
            ->orderBy('id')
            ->get();

        // if none found, offer one empty row
        $this->alerts = $rows->isEmpty()
            ? [['id' => uniqid(), 'text' => '', 'viewable' => []]]
            : $rows->map(fn ($row) => [
                  'id'       => uniqid(),
                  'text'     => $row->alert,
                  'viewable' => explode('|', $row->viewable ?? ''),
              ])->all();
    }

    /* ───────── Helpers ───────── */
    public function addAlert(): void
    { 
        $this->alerts[] = [
            'id' => uniqid(),
            'text' => '', 
            'viewable' => []
        ]; 
    }
    
    public function removeAlert($idx): void
    { 
        unset($this->alerts[$idx]); 
        $this->alerts = array_values($this->alerts); 
    }

    /* ───────── Validation rules ───────── */
    protected function rules(): array
    {
        return [
            'txtcust'            => 'required',
            'pno'                => 'required',
            'rev_inp'            => 'nullable|string',
            'alerts.*.text'      => 'required|string',
            'alerts.*.viewable'  => 'array',
        ];
    }

    /* ───────── Save (update) ───────── */
    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            // Delete alerts for the ORIGINAL part (from route params)
            alerts_tb::where('customer', $this->customer)
                ->where('part_no', $this->part)
                ->where('rev', $this->rev)
                ->where('atype', 'p')
                ->delete();

            // Insert new alerts (could be for same or different part after search)
            foreach ($this->alerts as $alert) {
                alerts_tb::create([
                    'customer' => $this->txtcust,
                    'part_no'  => $this->pno,
                    'rev'      => $this->rev_inp,
                    'alert'    => trim($alert['text']),
                    'viewable' => implode('|', $alert['viewable']),
                    'atype'    => 'p',
                ]);
            }
        });

        // Redirect without flash message
        return redirect(route('customers.alerts.manage-part'));
    }

    /* ───────── View ───────── */
    public function render()
    {
        return view('livewire.customers.alerts.editpartnumber')
            ->layout('layouts.app', ['title' => 'Edit Part‑Number Alerts']);
    }
}