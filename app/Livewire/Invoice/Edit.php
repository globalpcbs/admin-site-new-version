<?php

namespace App\Livewire\Invoice;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\data_tb as Customer;
use App\Models\shipper_tb as Shipper;
use App\Models\invoice_tb as Invoice;
use App\Models\rep_tb as reps;
use App\Models\invoice_items_tb as InvoiceItem;
use App\Models\order_tb as Order;
// for alerts ..
use App\Models\alerts_tb    as Alert;
use App\Models\profile_tb as Profile;
use App\Models\profile_tb2 as ProfileDetail;

class Edit extends Component
{
    public $invoiceId;
    public $vid, $sid, $namereq, $svia, $svia_oth, $fcharge, $city, $state, $sterms, $comments;
    public $commission, $salesrep, $customer, $part_no, $rev, $oo, $po, $ord_by, $lyrcnt, $delto, $date1, $stax;
    public $items = [], $total = 0, $totalCommission = 0;
    public $invoice; 
    public $reps;
    public $search = '';
    public $matches = [];
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
        $this->invoiceId = $id;
        $this->invoice = Invoice::with('items')->findOrFail($id);
        $this->vid         = $this->invoice->vid;
        $this->sid         = $this->invoice->sid;
        $this->namereq     = $this->invoice->namereq;
        $this->svia        = $this->invoice->svia;
        $this->svia_oth    = $this->invoice->svia_oth;
        $this->fcharge     = $this->invoice->fcharge;
        $this->salesrep    = $this->invoice->salesrep;
        $this->city        = $this->invoice->city;
        $this->state       = $this->invoice->state;
        $this->sterms      = $this->invoice->sterm;
        $this->comments    = $this->invoice->comments;
        $this->customer    = $this->invoice->customer;
        $this->part_no     = $this->invoice->part_no;
        $this->rev         = $this->invoice->rev;
        $this->oo          = $this->invoice->our_ord_num;
        $this->po          = $this->invoice->po;
        $this->ord_by      = $this->invoice->ord_by;
        $this->lyrcnt      = $this->invoice->no_layer;
        $this->delto       = $this->invoice->delto;
        $this->date1       = $this->invoice->date1;
        $this->stax        = $this->invoice->saletax;
        $this->commission  = $this->invoice->commision;

        $this->items = [];
      //  dd($this->invoice->items);
        foreach ($this->invoice->items as $item) {
            $this->items[] = [
                'item'        => $item->item,
                'description' => $item->itemdesc,
                'qty'         => $item->qty2,
                'unit_price'  => $item->uprice,
                'commission'  => (bool) $item->commision,
            ];
        }

        $this->items = array_pad($this->items, 6, [
            'item' => '',
            'description' => '',
            'qty' => null,
            'unit_price' => null,
            'commission' => false,
        ]);

        $this->reps = reps::all();
        $this->calculateTotals();
    }

    public function updated($property)
    {
        if (str_starts_with($property, 'items.') || $property === 'commission') {
            $this->calculateTotals();
        }
    }

    public function calculateTotals()
    {
        $this->total = 0;
        $this->totalCommission = 0;

        foreach ($this->items as $row) {
            $qty = (float) ($row['qty'] ?? 0);
            $price = (float) str_replace(',', '', $row['unit_price'] ?? 0);
            $lineTotal = $qty * $price;

            $this->total += $lineTotal;
            if (!empty($row['item']) && $row['commission']) {
                $this->totalCommission += ($lineTotal * ($this->commission / 100));
            }
        }
    }

    public function update()
    {
                 $this->validate([
            'vid' => ['required'],
            'sid' => ['required'],
            'sterms' => ['required'],
            'commission' => ['nullable', 'numeric'],
            'items' => ['array', 'size:6'],
            'items.*.item' => ['nullable', 'string'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.qty' => ['nullable', 'numeric'],
            'items.*.unit_price' => ['nullable', 'numeric'],
            'items.*.commission' => ['boolean'],
        ]);
        // dd("wewq");
        $this->button_status = 1;
        $alerts = Alert::where('customer', $this->customer)
            ->where('part_no', $this->part_no)
            ->where('rev', $this->rev)
            ->where('atype', 'p')
            ->orderBy('id', 'desc')
            ->get()
            ->filter(function ($alert) {
                return in_array('inv', explode('|', $alert->viewable));
            });
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
       // $this->saveproccess;
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
                return in_array('inv', explode('|', $alert->viewable));
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

        DB::transaction(function () {
            $invoice = Invoice::findOrFail($this->invoiceId);
            $invoice->update([
                'vid' => $this->vid,
                'sid' => $this->sid,
                'namereq' => $this->namereq,
                'svia' => $this->svia,
                'svia_oth' => $this->svia === 'Other' ? $this->svia_oth : null,
                'fcharge' => $this->fcharge ?: 0,
                'salesrep' => $this->salesrep,
                'city' => $this->city,
                'state' => $this->state,
                'sterm' => $this->sterms,
                'comments' => $this->comments,
                'customer' => $this->customer,
                'part_no' => $this->part_no,
                'rev' => $this->rev,
                'our_ord_num' => $this->oo,
                'po' => $this->po,
                'ord_by' => $this->ord_by,
                'no_layer' => $this->lyrcnt,
                'delto' => $this->delto,
                'date1' => $this->date1,
                'saletax' => $this->stax ?: 0,
                'commision' => $this->commission,
                'comval' => $this->totalCommission,
            ]);
           // dd($this->items);
            InvoiceItem::where('pid', $invoice->invoice_id)->delete();
            foreach ($this->items as $row) {
                if (!empty($row['item'])) {
                    $qty = (float) $row['qty'];
                    $uprice = (float) str_replace(',', '', $row['unit_price']);

                    $item = new InvoiceItem();
                    $item->pid = $invoice->invoice_id;
                    $item->item = $row['item'];
                    $item->itemdesc = $row['description'] ?? 'Hello World';
                    $item->qty2 = $qty;
                    $item->uprice = $uprice;
                    $item->commision = !empty($row['commission']) ? 1 : 0;
                    $item->tprice = $qty * $uprice;
        
                    $item->save();
                }
            }
        });

        session()->flash('success', 'Invoice updated successfully!');
        return redirect(route('invoice.manage'));
    }
    public function render()
    {
        return view('livewire.invoice.edit', [
            'customers' => Customer::orderBy('c_name')->get(),
            'shippers' => Shipper::orderBy('c_name')->get(),
        ])->layout('layouts.app', ['title' => 'Edit Invoice']);
    }
    public function lineTotal($index)
    {
        $row = $this->items[$index] ?? ['qty' => 0, 'unit_price' => 0];
        $qty = floatval($row['qty'] ?? 0);
        $unit = floatval($row['unit_price'] ?? 0);
        return $qty * $unit;
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
         $this->customer = $order->cust_name;
         $this->rev = $order->rev;
         $this->part_no = $order->part_no;
         $this->lyrcnt = $order->no_layer;
         $this->ord_by = $order->req_by;
        // dd($this->customer);
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