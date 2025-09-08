<?php

namespace App\Livewire\PurchaseOrder;

use Livewire\Component;
use App\Models\data_tb as Customer;
use App\Models\shipper_tb;
use App\Models\vendor_tb;
use App\Models\porder_tb;
use App\Models\items_tb;
use App\Models\order_tb as Order;
// for alerts ..
use App\Models\alerts_tb    as Alert;
use App\Models\profile_tb as Profile;
use App\Models\profile_tb2 as ProfileDetail;
use App\Models\profile_vendor_tb as ProfileVendor;
use App\Models\profile_vendor_tb2 as ProfileVendor2;

class Edit extends Component
{
    public $poid;

    public $vid, $sid, $namereq, $namereq1, $svia, $svia_oth, $city, $state;
    public $sterms, $rohs, $comments, $podate, $customer, $part_no, $rev;
    public $date1, $date2, $cpo, $dweek, $no_layer, $cancharge, $ordon, $iscancel, $ccharge;
    public $specialreqval;

    public $items = [];
    public $total = 0;

    public $search = '';
    public $matches = [];
    public $inputKey;
            // for alerts 
    public bool $showAlertPopup = false;
    public $alertMessages = [];
    public bool $showProfilePopup = false;
    public $profileMessages = [];

    // Alert management properties
    public $newAlert = '';
    public $editingAlertId = null;
    // Add these properties
    public bool $showVendorAlertPopup = false;
    public $vendorAlertMessages = [];
    
    public $button_status = 0;

    public function mount($id)
    {
        $po = porder_tb::findOrFail($id);
        $this->poid = $po->poid;

        $this->vid = $po->vid;
        $this->sid = $po->sid;
        $this->namereq = $po->namereq;
        $this->namereq1 = $po->namereq1;
        $this->svia = $po->svia;
        $this->svia_oth = $po->svia_oth;
        $this->city = $po->city;
        $this->state = $po->state;
        $this->sterms = $po->sterms;
        $this->rohs = $po->rohs;
        $this->comments = $po->comments;
        $this->podate = $po->podate;
        $this->customer = $po->customer;
        $this->part_no = $po->part_no;
        $this->rev = $po->rev;
        $this->date1 = $po->date1;
        $this->date2 = $po->date2;
        $this->cpo = $po->po;
        $this->dweek = $po->dweek;
        $this->no_layer = $po->no_layer;
        $this->cancharge = $po->cancharge;
        $this->ordon = $po->ordon;
      //  dd($po->date1);
        $this->iscancel = $po->iscancel;
        $this->ccharge = $po->ccharge;
        $this->specialreqval = $po->sp_reqs;

        $this->items = items_tb::where('pid', $po->poid)
            ->get()
            ->map(function ($item) {
                return [
                    'item' => $item->item,
                    'desc' => $item->itemdesc,
                    'dpdesc' => $item->dpval,
                    'qty' => $item->qty2,
                    'uprice' => $item->uprice,
                ];
            })
            ->toArray();

        // Fill up to 6 rows
        while (count($this->items) < 6) {
            $this->items[] = ['item' => '', 'desc' => '', 'dpdesc' => '', 'qty' => '', 'uprice' => ''];
        }

        $this->recalculateTotal();
        $this->inputKey = uniqid();
    }

    public function updatedItems($value, $key)
    {
        $this->recalculateTotal();
    }

    public function recalculateTotal()
    {
        $this->total = collect($this->items)->sum(function ($item) {
            return (float) ($item['qty'] ?? 0) * (float) ($item['uprice'] ?? 0);
        });
    }

    public function update()
    {
       $customer = Customer::where('c_name', $this->customer)->first();
        $this->customer_id = $customer ? $customer->data_id : null;
        $this->button_status = 1;
       // dd($this->customer_id);
          $alerts = Alert::where('customer', $this->customer)
                ->where('part_no', $this->part_no)
                ->where('rev', $this->rev)
                ->where('atype', 'p')
                ->orderBy('id', 'desc')
                ->get()
                ->filter(function ($alert) {
                    return in_array('po', explode('|', $alert->viewable));
                });
                // for profile alert ..
            // Check for profile alerts
            $profiles = Profile::where('custid',$this->customer_id)->with('details')
                ->get();
        // dd($profiles->count());
            $hasAlerts = $alerts->count() > 0;
            $hasProfiles = $profiles->count() > 0;
            // Add this to your save() method, before checking other alerts
            $vendorAlerts = ProfileVendor::where('custid', $this->vid)
                ->with('requirements')
                ->get()
                ->filter(function($profile) {
                    return $profile->requirements->isNotEmpty();
                });

            $hasVendorAlerts = $vendorAlerts->count() > 0;

            if ($hasVendorAlerts) {
                $this->showVendorAlertPopup = true;
                $this->vendorAlertMessages = $vendorAlerts;
            }
            if ($hasAlerts) {
                $this->showAlertPopup = true;
                $this->alertMessages = $alerts;
            }

            if ($hasProfiles) {
                $this->showProfilePopup = true;
                $this->profileMessages = $profiles;
            }

            // If no alerts at all, save immediately
            if (!$hasAlerts && !$hasProfiles) {
                $this->saveproccess();
            }
    }
    public function closeVendorAlertPopup(): void
    {
        $this->showVendorAlertPopup = false;
        $this->checkIfShouldSave();
    }
     public function closeAlertPopup(): void
        {
            $this->showAlertPopup = false;
            // dd($this->showAlertPopup);
            $this->checkIfShouldSave();
        }

        public function closeProfilePopup(): void
        {
            $this->showProfilePopup = false;
            //  dd($this->showProfilePopup);
            $this->checkIfShouldSave();
        }

        protected function checkIfShouldSave(): void
        {
            // Only save if all popups are closed
            if (!$this->showAlertPopup && !$this->showProfilePopup && !$this->showVendorAlertPopup) {
                $this->saveproccess();
            }
        }
                public array $alertTypes = [];
        public function addAlert(): void
        {
            $this->validate([
                'newAlert' => 'required|string|max:255',
                'alertTypes' => 'required|array|min:1'
            ]);

            // Debug before save
            logger()->debug('Pre-Save Data', [
                'alert' => $this->newAlert,
                'types' => $this->alertTypes,
                'imploded' => collect($this->alertTypes)->implode('|')
            ]);

            try {
            // dd($this->alertTypes);
                $alert = Alert::create([
                    'customer' => $this->customer ?? '',
                    'part_no' => $this->part_no ?? '',
                    'rev' => $this->rev ?? '',
                    'alert' => trim($this->newAlert),
                    'viewable' => collect($this->alertTypes)->implode('|'),
                    'atype' => 'p',
                ]);

                // Debug after save
                logger()->debug('Created Alert', $alert->toArray());

                $this->reset(['newAlert', 'alertTypes']);
                $this->loadAlerts();
                session()->flash('success', 'Alert added successfully.');
                
            } catch (\Exception $e) {
                logger()->error('Alert Creation Error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                session()->flash('error', 'Failed to add alert. Check logs for details.');
            }
        }


        public function loadAlerts()
        {
        $alerts = Alert::where('customer', $this->customer)
            ->where('part_no', $this->part_no)
            ->where('rev', $this->rev)
            ->where('atype', 'p')
            ->orderBy('id', 'desc')
            ->get()
            ->filter(function ($alert) {
                return in_array('po', explode('|', $alert->viewable));
            });

            if($alerts->count() > 0) {
                //$this->showAlertPopup = true;
                $this->alertMessages = $alerts;
            }
        }

        public function editAlert($id)
        {
            $alert = Alert::findOrFail($id);
            
            $this->editingAlertId = $id;
            $this->newAlert = $alert->alert;
            
            // Clear the array first
            $this->alertTypes = [];
            
            // Small delay to ensure Livewire processes the change
            usleep(1000);
            
            // Set the new values
            $this->alertTypes = collect(explode('|', $alert->viewable))
                ->map(fn($item) => trim($item))
                ->filter()
                ->values()
                ->toArray();
            
            // Force Livewire to update the view
            $this->js('window.dispatchEvent(new CustomEvent("alert-types-updated"))');
        }


        public function updateAlert()
        {
            $this->validate(['newAlert' => 'required|string|max:255']);
            //dd($this->newAlert);
            $viewable = collect($this->alertTypes)->filter()->implode('|');

            Alert::where('id', $this->editingAlertId)->update([
                'alert' => trim($this->newAlert),
                'viewable' => $viewable,
            ]);

            $this->reset(['newAlert', 'alertTypes', 'editingAlertId']);
            $this->loadAlerts();
        }


        public function deleteAlert($id)
        {
            Alert::where('id', $id)->delete();
            $this->loadAlerts();
        }

        public function cancelEdit()
        {
            $this->resetAlertInputs();
        }

        public function resetAlertInputs()
        {
            $this->reset(['newAlert','alertTypes']);
        }
    public function saveproccess(){
         $po = porder_tb::findOrFail($this->poid);

        $po->update([
            'vid' => $this->vid,
            'sid' => $this->sid,
            'namereq' => $this->namereq,
            'namereq1' => $this->namereq1,
            'svia' => $this->svia,
            'svia_oth' => $this->svia_oth,
            'city' => $this->city,
            'state' => $this->state,
            'sterms' => $this->sterms,
            'rohs' => $this->rohs,
            'comments' => $this->comments,
            'customer' => $this->customer,
            'part_no' => $this->part_no,
            'rev' => $this->rev,
            'date1' => $this->date1,
            'date2' => $this->date2,
            'po' => $this->cpo,
            'dweek' => substr($this->date1, 11),
            'no_layer' => $this->no_layer,
            'cancharge' => $this->cancharge,
            'ordon' => $this->ordon,
            'iscancel' => $this->iscancel,
            'ccharge' => $this->ccharge,
            'sp_reqs' => $this->specialreqval,
        ]);

        items_tb::where('pid', $this->poid)->delete();

        foreach ($this->items as $item) {
            if (!empty($item['item'])) {
                $qty = floatval($item['qty']);
                $uprice = floatval($item['uprice']);
                $tprice = $qty * $uprice;

                items_tb::create([
                    'pid' => $this->poid,
                    'item' => $item['item'],
                    'itemdesc' => $item['desc'],
                    'qty2' => $qty,
                    'uprice' => $uprice,
                    'tprice' => $tprice,
                    'dpval' => $item['dpdesc'],
                ]);
            }
        }

        session()->flash('success', 'Purchase order updated.');
        return redirect()->route('purchase.orders.manage');
    }

    public function onKeyUp(string $value)
    {
        $this->search = $value;

        if (strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }

        $this->matches = Order::query()
            ->select('part_no', 'rev', 'cust_name')
            ->where('part_no', 'like', "%{$value}%")
            ->orWhere('cust_name', 'like', "%{$value}%")
            ->distinct()
            ->get()
            ->map(fn ($row) => [
                'label' => "{$row->part_no}_{$row->rev}_{$row->cust_name}",
                'part' => $row->part_no,
                'rev' => $row->rev,
                'cust' => $row->cust_name,
            ])
            ->toArray();
    }

    public function useMatch(int $i)
    {
        if (!isset($this->matches[$i])) return;

        $m = $this->matches[$i];
        $this->selectLookup($m['part'], $m['rev'], $m['cust']);
    }

    public function selectLookup($part, $rev, $cust)
    {
        $this->search = "{$cust}_{$part}_{$rev}";
        $this->matches = [];

        $order = Order::where('part_no', $part)
            ->where('rev', $rev)
            ->where('cust_name', $cust)
            ->first();
            

        if ($order) {
            $this->part_no = $order->part_no;
            $this->rev = $order->rev;
            $this->customer = $order->cust_name;
            $this->cpo = $order->cpo;
            $this->no_layer = $order->no_layer;
            $this->inputKey = uniqid();
        }
    }

    public function render()
    {
        return view('livewire.purchase-order.edit', [
            'vendors' => vendor_tb::orderBy('c_name')->get(),
            'shippers' => shipper_tb::orderBy('c_name')->get(),
            'customers' => Customer::where('c_name', '!=', '')->orderBy('c_name')->get(),
        ])->layout('layouts.app');
    }
}