<?php

namespace App\Livewire\PurchaseOrder;

use Livewire\Component;
use App\Models\data_tb as Customer;
use App\Models\shipper_tb;
use App\Models\vendor_tb;
use App\Models\porder_tb;
use App\Models\items_tb;
use App\Models\order_tb     as Order;
use Illuminate\Support\Facades\DB;
// for alerts ..
use App\Models\alerts_tb    as Alert;
use App\Models\profile_tb as Profile;
use App\Models\profile_tb2 as ProfileDetail;
use App\Models\profile_vendor_tb as ProfileVendor;
use App\Models\profile_vendor_tb2 as ProfileVendor2;

class Add extends Component
{
    public $vid;
    public $sid;

    public $namereq;
    public $namereq1;

    public $svia = 'Fedex';
    public $svia_oth;

    public $city;
    public $state;

    public $sterms = 'Prepaid';
    public $rohs = 'no';

    public $comments;

    public $podate;
    public $customer = '';
    public $part_no = '';
    public $rev = '';

    public $date1;
    public $date2;

    public $cpo = '';
    public $dweek;

    public $no_layer = '';

    public $cancharge = 'no';
    public $ordon;
    public $iscancel = 'no';
    public $ccharge;

    public $specialreqval;

    public $items = [];
    public $total = 0;

    /* ─── existing public props … ───────────────────────────────────── */
    public $search     = '';          // what the user is typing
    public $matches    = [];          // array of suggestions ⬅️  NEW
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
    public function mount()
    {
        $this->podate = date('Y-m-d');
        $this->items = array_fill(0, 6, [
            'item' => '',
            'dpdesc' => '',
            'desc' => '',
            'qty' => '',
            'uprice' => '',
        ]);
        $this->recalculateTotal();
        $this->inputKey = uniqid(); // force unique key
        $this->total = 0;
    }
    public function updatedItems($value, $key)
    {
        // This triggers anytime any nested field in items is updated
        $this->recalculateTotal();
    }

    public function recalculateTotal()
    {
        $this->total = collect($this->items)->sum(function ($item) {
            $qty = (float) ($item['qty'] ?? 0);
            $price = (float) ($item['uprice'] ?? 0);
            return $qty * $price;
        });
    }
    public function save(){
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
      //  $this->saveproccess();
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
            $po = porder_tb::create([
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
            'podate' => now()->format('m/d/Y'),
            'customer' => $this->customer,
            'part_no' => $this->part_no,
            'rev' => $this->rev,
            'date1' => $this->date1,
            'date2' => $this->date2,
            'po' => $this->cpo,
            'dweek' => substr($this->date1, 11), // placeholder logic
            'no_layer' => $this->no_layer,
            'cancharge' => $this->cancharge,
            'ordon' => $this->ordon,
            'iscancel' => $this->iscancel,
            'ccharge' => $this->ccharge,
            'sp_reqs' => $this->specialreqval,
        ]);

        foreach ($this->items as $item) {
            if ($item['item'] !== '') {
                $uprice = floatval(str_replace(',', '', $item['uprice']));
                $qty = floatval(str_replace(',', '', $item['qty']));
                $tprice = $uprice * $qty;

                items_tb::create([
                    'item' => $item['item'],
                    'itemdesc' => $item['desc'],
                    'qty2' => $qty,
                    'uprice' => $uprice,
                    'tprice' => $tprice,
                    'pid' => $po->poid,
                    'dpval' => $item['dpdesc'],
                ]);
            }
        }

        session()->flash('success', 'Purchase order saved.');
        return redirect(route('purchase.orders.manage'));

    }

    public function render()
    {
        return view('livewire.purchase-order.add', [
            'vendors' => vendor_tb::orderBy('c_name')->get(),
            'shippers' => shipper_tb::orderBy('c_name')->get(),
            'customers' => Customer::where('c_name', '!=', '')->orderBy('c_name')->get(),
        ])->layout('layouts.app');
    }
    // Search as user types
    public function onKeyUp(string $value)
    {
        $this->search = $value;

        if (mb_strlen(trim($value)) < 2) {
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
        // dd($order);
            // 👇 this will force Livewire to re-render inputs
            $this->inputKey = uniqid();
        }
    }


}