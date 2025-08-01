<?php

namespace App\Livewire\Customers\Alerts;

use App\Models\alerts_tb;
use App\Models\data_tb as customer;
use App\Models\order_tb;
use Livewire\Component;
use Illuminate\Support\Str;

class AddPartNumberAlerts extends Component
{
    public $aid = '';
    public $txtcust = '';
    public $pno = '';
    public $rev = '';
    public $alerts = [];
    public $prevaid = '';
    public $newpno = 'no';

    public function mount()
    {
        $this->addAlert();
    }

   public function loadExistingAlerts($selectedAid)
    {
        //dd($selectedAid);
        $this->aid = $selectedAid;    
        $order = order_tb::find($this->aid);
        if ($order) {
            $this->rev = $order->rev;
           // dd($this->rev);
            $this->txtcust = $order->cust_name;
          //  dd($this->txtcust);
            //dd($this->txtcust);
            $this->pno = $order->part_no;
        }

    //     $alerts = alerts_tb::whereRaw("TRIM(customer) = ? AND TRIM(part_no) = ? AND TRIM(rev) = ?", [
    //             $order->cust_name, $order->part_no, $order->rev
    //         ])
    //         ->where('atype', 'p')
    //         ->get();

    //   //  $this->alerts = [];
    //     foreach ($alerts as $alert) {
    //         $this->alerts[] = [
    //             'text' => $alert->alert,
    //             'viewable' => explode('|', $alert->viewable),
    //         ];
    //     }
    }



    public function addAlert()
    {
        $this->alerts[] = ['text' => '', 'viewable' => []];
    }

    public function removeAlert($index)
    {
        unset($this->alerts[$index]);
        $this->alerts = array_values($this->alerts); // reindex
    }

    public function save()
    {
        //dd($this->alerts);
        $this->validate([
            'txtcust' => 'required',
            'pno' => 'required',
            'rev' => 'nullable|string',
            'alerts.*.text' => 'required|string',
        ]);

        //dd("Worker");
        foreach ($this->alerts as $alert) {
            $viewable = implode('|', $alert['viewable']);
            if ($this->newpno == 'yes') {
                $viewable = 'quo';
            }

            $alertObj = new alerts_tb();
            $alertObj->customer = $this->txtcust;
            $alertObj->part_no = $this->pno;
            $alertObj->rev = $this->rev;
            $alertObj->alert = trim($alert['text']);
            $alertObj->viewable = $viewable;
            $alertObj->atype = 'p';

            $alertObj->save();
        }

        session()->flash('success', 'Alerts saved successfully.');
        return redirect(route('customers.alerts.manage-part'));
        //return redirect()->route('alerts.manage');
    }

    public function render()
    {
        return view('livewire.customers.alerts.add-part-number-alerts', [
            'customers' => customer::orderBy('c_name')->get(),
            'aidOptions' => order_tb::orderby('cust_name')
                ->get(),
        ])->layout('layouts.app', ['title' => 'Add Part Number Alert']);
    }
}