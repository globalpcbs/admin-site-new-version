<?php

namespace App\Livewire\Customers\Alerts;

use Livewire\Component;
use App\Models\alerts_tb;
use Illuminate\Support\Facades\DB;

class Editpartnumber extends Component
{
    /* ───────── Route‑bound params ───────── */
    public string $customer;
    public string $part;
    public string $rev = '';     // may arrive empty

    /* ───────── Form fields ───────── */
    public string $txtcust = '';
    public string $pno     = '';
    public string $rev_inp = '';  // separate because $rev comes from route
    public array  $alerts  = [];  // [['text'=>'…','viewable'=>['quo','po']] …]

    /* ───────── Mount: preload existing data ───────── */
    public function mount(string $customer, string $part, string $rev = ''): void
    {
        $this->customer = $this->txtcust = trim($customer);
        $this->part     = $this->pno     = trim($part);
        $this->rev      = $this->rev_inp = trim($rev);

        // fetch current alerts for this trio
        $rows = alerts_tb::whereRaw('TRIM(customer)=?', [$this->customer])
            ->whereRaw('TRIM(part_no)=?', [$this->part])
            ->whereRaw('TRIM(rev)=?', [$this->rev])
            ->where('atype', 'p')
            ->orderBy('id')
            ->get();

        // if none found, offer one empty row
        $this->alerts = $rows->isEmpty()
            ? [['text' => '', 'viewable' => []]]
            : $rows->map(fn ($row) => [
                   'id'       => uniqid(),
                  'text'     => $row->alert,
                  'viewable' => explode('|', $row->viewable ?? ''),
              ])->all();
    }

    /* ───────── Helpers ───────── */
    public function addAlert()          { 
        $this->alerts[] = [
            'id' => uniqid(),   // unique key
            'text'=>'', 
            'viewable'=>[]
        ]; 
    }
    public function removeAlert($idx)   
    { 
       // dd($idx);
        unset($this->alerts[$idx]); $this->alerts = array_values($this->alerts); 
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
            // wipe existing rows
            alerts_tb::where('customer', $this->customer)
                ->where('part_no',  $this->part)
                ->where('rev',      $this->rev)
                ->delete();

            // re‑insert
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

        session()->flash('success', 'Alerts updated successfully.');
      //  $this->redirectRoute('alerts.manage');   // ← adjust if list route named differently
        return redirect(route('customers.alerts.manage-part'));

    }

    /* ───────── View ───────── */
    public function render()
    {
        return view('livewire.customers.alerts.editpartnumber')
            ->layout('layouts.app', ['title' => 'Edit Part‑Number Alerts']);
    }
}