<?php

namespace App\Livewire\PackingSlips;

use Livewire\Component;
use App\Models\packing_tb;
use App\Models\packing_items_tb as items;
use App\Models\profile_tb3;
use App\Models\temp_profile2;
use App\Models\maincont_packing;
use App\Models\data_tb;
use App\Models\shipper_tb;
use App\Models\maincont_tb;
use Illuminate\Support\Facades\DB;
use App\Models\order_tb as Order;
// for alerts ..
use App\Models\alerts_tb    as Alert;
use App\Models\profile_tb as Profile;
use App\Models\profile_tb2 as ProfileDetail;

class Edit extends Component
{
    public $packingId;
    public $vid, $sid, $namereq, $svia_oth, $fcharge, $city, $state, $sterms, $comments, $customer, $odate;
    public $svia, $saletax, $no_layer, $specialreqval, $commission;
    public $customer_look = '', $part_no = '', $rev = '', $oo = '', $po = '', $ord_by = '';
    public $lyrcnt = '', $delto = '', $date1 = '', $stax = '', $specialreq = '', $alertHtml = '';
    public $items = [], $maincontacts = [], $selectedMainContacts = [], $search = '', $matches = [];
              // for alerts 
    public bool $showAlertPopup = false;
    public $alertMessages = [];
    public bool $showProfilePopup = false;
    public $profileMessages = [];

    // Alert management properties
    public $newAlert = '';
    public $editingAlertId = null;
    
    public $button_status = 0;

    public function mount($id)
    {
        $this->packingId = $id;
        $packing = packing_tb::with(['items'])->findOrFail($id);

        $this->vid = $packing->vid;
        $this->sid = $packing->sid;
        $this->namereq = $packing->namereq;
        $this->svia = $packing->svia;
        $this->svia_oth = $packing->svia_oth;
        $this->fcharge = $packing->fcharge ?: null;
        $this->city = $packing->city;
        $this->state = $packing->state;
        $this->sterms = $packing->sterm;
        $this->comments = $packing->comments;
        $this->customer = $packing->customer;
        $this->part_no = $packing->part_no;
        $this->rev = $packing->rev;
        $this->delto = $packing->delto;
        $this->date1 = $packing->date1;
        $this->odate = $packing->odate;
        $this->po = $packing->po;
        $this->oo = $packing->our_ord_num;
        $this->saletax = $packing->saletax;
        $this->no_layer = $packing->no_layer;
        $this->specialreqval = $packing->sp_reqs;

        $this->items = $packing->items->map(function ($item) {
            return [
                'item' => $item->item,
                'desc' => $item->itemdesc,
                'qty' => $item->qty2,
                'shipqty' => $item->shipqty,
            ];
        })->toArray();

        $this->maincontacts = maincont_tb::where('coustid', $this->customer)->get();

        $this->selectedMainContacts = maincont_packing::where('packingid', $id)
            ->pluck('maincontid')
            ->toArray();
    }

    public function mainContact()
    {
        $this->maincontacts = maincont_tb::where('coustid', $this->customer)->get();
        $this->selectedMainContacts = [];
    }

    public function getTotalOrderedProperty()
    {
        return collect($this->items)->sum(fn ($item) => floatval(str_replace(',', '', $item['qty'] ?? 0)));
    }

    public function getTotalShippedProperty()
    {
        return collect($this->items)->sum(fn ($item) => floatval(str_replace(',', '', $item['shipqty'] ?? 0)));
    }

    public function update()
    {
            $this->button_status = 1;
           $alerts = Alert::where('customer', $this->customer_look)
                ->where('part_no', $this->part_no)
                ->where('rev', $this->rev)
                ->where('atype', 'p')
                ->orderBy('id', 'desc')
                ->get()
                ->filter(function ($alert) {
                    return in_array('pac', explode('|', $alert->viewable));
                });
           // dd($alerts->count());
                // for profile alert ..
            // Check for profile alerts
            $profiles = Profile::where('custid',$this->vid)->with('details')
                ->get();
        // dd($profiles->count());
            $hasAlerts = $alerts->count() > 0;
            $hasProfiles = $profiles->count() > 0;

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
            // Only save if both popups are closed
            if (!$this->showAlertPopup && !$this->showProfilePopup) {
            // dd("main save function");
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
                    'customer' => $this->customer_look ?? '',
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
        $alerts = Alert::where('customer', $this->customer_look)
                ->where('part_no', $this->part_no)
                ->where('rev', $this->rev)
                ->where('atype', 'p')
                ->orderBy('id', 'desc')
                ->get()
                ->filter(function ($alert) {
                    return in_array('pac', explode('|', $alert->viewable));
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
        DB::beginTransaction();

        try {
            $packing = packing_tb::findOrFail($this->packingId);

            $packing->vid = $this->vid;
            $packing->sid = $this->sid;
            $packing->namereq = $this->namereq;
            $packing->svia = $this->svia;
            $packing->svia_oth = $this->svia === 'Other' ? $this->svia_oth : null;
            $packing->fcharge = $this->fcharge ?: null;
            $packing->city = $this->city;
            $packing->state = $this->state;
            $packing->sterm = $this->sterms;
            $packing->comments = $this->comments;
            $packing->customer = $this->customer_look;
            $packing->part_no = $this->part_no;
            $packing->rev = $this->rev;
            $packing->delto = $this->delto;
            $packing->date1 = $this->date1;
            $packing->odate = $this->odate;
            $packing->po = $this->po;
            $packing->our_ord_num = $this->oo;
            $packing->saletax = $this->saletax;
            $packing->no_layer = $this->no_layer;
            $packing->sp_reqs = $this->specialreqval;
            $packing->save();

            items::where('pid', $packing->invoice_id)->delete();
            foreach ($this->items as $row) {
                if (trim($row['item']) === '') continue;

                items::create([
                    'item' => $row['item'],
                    'itemdesc' => $row['desc'],
                    'qty2' => str_replace(',', '', $row['qty']),
                    'shipqty' => str_replace(',', '', $row['shipqty']),
                    'pid' => $packing->invoice_id,
                ]);
            }

            maincont_packing::where('packingid', $packing->invoice_id)->delete();
            foreach ($this->selectedMainContacts as $contactId) {
                maincont_packing::create([
                    'maincontid' => $contactId,
                    'packingid' => $packing->invoice_id,
                ]);
            }

            DB::commit();
            session()->flash('success', 'Packing Slip Updated Successfully!');
            return redirect()->route('packing.manage');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('warning', 'Update failed: ' . $e->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.packing-slips.edit', [
            'customers' => data_tb::orderBy('c_name')->get(),
            'shippers' => shipper_tb::orderBy('c_name')->get(),
            'totalOrdered' => $this->totalOrdered,
            'totalShipped' => $this->totalShipped,
        ])->layout('layouts.app', ['title' => 'Edit Packing Slip']);
    }
    public function onKeyUp(string $value): void
    {
        $this->search = $value;                 // keep the box in sync

        if (mb_strlen(trim($value)) < 2) {      // ignore 0‑1 chars
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
                'part'  => $row->part_no,
                'rev'   => $row->rev,
                'cust'  => $row->cust_name,
            ])->toArray();
    }
     /* user clicked a suggestion */
     public function selectLookup(string $part, string $rev, string $cust): void
     {
        //  $this->part_no  = $part;                  // fill your real fields
        //  $this->rev      = $rev;
        //  $this->customer = $cust;
         
         $this->search   = "{$cust}_{$part}_{$rev}"; // optional: show chosen string
         $this->matches  = [];                      // hide dropdown
         $order = Order::where('part_no',  $part)
         ->where('rev',      $rev)
         ->where('cust_name',$cust)
         ->first();
         $this->customer_look = $order->cust_name;
         $this->rev = $order->rev;
         $this->part_no = $order->part_no;
         $this->lyrcnt = $order->no_layer;
         $this->ord_by = $order->req_by;
       //  dd($this->customer_look);
     }
     /** Handle the click coming from <li wire:click="useMatch($i)"> */
    public function useMatch(int $i): void
    {
        if (! isset($this->matches[$i])) {
            return;                     // out‑of‑bounds guard
        }

        $m = $this->matches[$i];

        $this->selectLookup($m['part'], $m['rev'], $m['cust']);
    }
}