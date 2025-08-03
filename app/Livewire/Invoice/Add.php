<?php

namespace App\Livewire\Invoice;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\data_tb as Customer;
use App\Models\shipper_tb as Shipper;
use App\Models\invoice_tb as Invoice;
use App\Models\rep_tb as reps;
use App\Models\invoice_items_tb as InvoiceItem;
use App\Models\order_tb     as Order;
// for alerts ..
use App\Models\alerts_tb    as Alert;
use App\Models\profile_tb as Profile;
use App\Models\profile_tb2 as ProfileDetail;

class Add extends Component
{
    public $vid = '';
    public $sid = '';
    public $namereq = '';
    public $svia = 'Personal Delivery';
    public $svia_oth = '';
    public $fcharge = '';
    public $city = '';
    public $state = '';
    public $sterms = 'Prepaid';
    public $comments = '';
   
    public $commission = '0';
    public $salesrep;
     // Lookup / line‑item‑related
     public $customer = '';
     public $part_no      = '';
     public $rev          = '';
     public $oo           = '';    // our PO
     public $po           = '';    // customer PO
     public $ord_by       = '';
     public $lyrcnt       = '';
     public $delto        = '';
     public $date1        = '';    // delivered‑on (string, Y‑m‑d)
     public $stax         = '';
     public $specialreq   = '';    // value actually stored
     public $alertHtml    = '';    // rendered HTML shown to user
     

    public $reps;
    public $items = [];
    public $total = 0;
    public $totalCommission = 0;
     /* ─── existing public props … ───────────────────────────────────── */
     public $search     = '';          // what the user is typing
     public $matches    = [];          // array of suggestions ⬅️  NEW
     // alertt ..
      // for alerts 
    public bool $showAlertPopup = false;
    public $alertMessages = [];
    public bool $showProfilePopup = false;
    public $profileMessages = [];

    // Alert management properties
    public $newAlert = '';
    public $editingAlertId = null;

    public function mount()
    {
        $this->items = collect(range(1, 6))
            ->map(fn () => [
                'item' => '',
                'description' => '',
                'qty' => null,
                'unit_price' => null,
                'commission' => false,
            ])
            ->toArray();

        $this->reps = reps::all();
    }

    public function updated($property)
    {
        if (str_starts_with($property, 'items.') || $property === 'commission') {
            $this->calculateTotals();
        }
    }

    public function lineTotal($index)
    {
        $row = $this->items[$index] ?? ['qty' => 0, 'unit_price' => 0];
        $qty = floatval($row['qty'] ?? 0);
        $unit = floatval($row['unit_price'] ?? 0);
        return $qty * $unit;
    }

    public function calculateTotals()
    {
        $this->total = 0;
        $this->totalCommission = 0;

        foreach ($this->items as $index => $row) {
            $qty = (float) ($row['qty'] ?? 0);
            $uprice = (float) str_replace(',', '', $row['unit_price'] ?? 0);
            $lineTotal = $qty * $uprice;

            $this->total += $lineTotal;

            if (!empty($row['item']) && $row['commission']) {
                $this->totalCommission += ($lineTotal * ($this->commission / 100));
            }
        }
    }

        public function save()
        {
            $this->validate([
                'vid' => ['required', 'exists:data_tb,data_id'],
                'sid' => ['required'],
                'namereq' => ['nullable', 'string', 'max:100'],
                'svia' => ['required'],
                'svia_oth' => ['nullable', 'required_if:svia,Other', 'string', 'max:50'],
                'fcharge' => ['nullable', 'numeric'],
                'city' => ['nullable', 'string', 'max:100'],
                'state' => ['nullable', 'string', 'max:50'],
                'sterms' => ['required'],
                'comments' => ['nullable', 'string'],
                'customer' => ['nullable', 'string', 'max:100'],
                'part_no' => ['nullable', 'string', 'max:50'],
                'rev' => ['nullable', 'string', 'max:20'],
                'oo' => ['nullable', 'string', 'max:50'],
                'po' => ['nullable', 'string', 'max:50'],
                'ord_by' => ['nullable', 'string', 'max:50'],
                'lyrcnt' => ['nullable', 'string'],
                'delto' => ['nullable', 'string', 'max:100'],
                'date1' => ['nullable', 'date_format:Y-m-d'],
                'stax' => ['nullable', 'numeric'],
                'commission' => ['nullable', 'numeric'],
                'items' => ['array', 'size:6'],
                'items.*.item' => ['nullable', 'string', 'max:50'],
                'items.*.description' => ['nullable', 'string'],
                'items.*.qty' => ['nullable', 'numeric'],
                'items.*.unit_price' => ['nullable', 'numeric'],
                'items.*.commission' => ['boolean'],
            ]);
           // dd("wewq");
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
           // dd($this->items);
            DB::transaction(function () {
                $invoice = new Invoice();
                $invoice->vid        = $this->vid;
                $invoice->sid        = $this->sid;
                $invoice->namereq    = $this->namereq;
                $invoice->svia       = $this->svia;
                $invoice->svia_oth   = $this->svia === 'Other' ? $this->svia_oth : null;
                $invoice->fcharge    = $this->fcharge ?: 0;
                $invoice->salesrep   = $this->salesrep;
                $invoice->city       = $this->city;
                $invoice->state      = $this->state;
                $invoice->sterm      = $this->sterms;
                $invoice->comments   = $this->comments;
                $invoice->podate     = now()->format('m/d/Y');
                $invoice->customer   = $this->customer;
                $invoice->part_no    = $this->part_no;
                $invoice->rev        = $this->rev;
                $invoice->delto      = $this->delto;
                $invoice->ord_by     = $this->ord_by;
                $invoice->date1      = $this->date1;
                $invoice->po         = $this->po;
                $invoice->our_ord_num = $this->oo;
                $invoice->commision  = $this->commission;
                $invoice->saletax    = $this->stax ?: 0;
                $invoice->no_layer   = $this->lyrcnt;
                $invoice->comval     = $this->totalCommission;
                $invoice->save();
                try {
                   // dd($this->items);
                    foreach ($this->items as $index => $row) {
                        if (!empty($row['item'])) {
                            $qty = (float) ($row['qty'] ?? 0);
                            $uprice = (float) str_replace(',', '', $row['unit_price'] ?? 0);
                
                            $item = new InvoiceItem();
                            $item->pid = $invoice->invoice_id;
                            $item->item = $row['item'];
                            $item->itemdesc = $row['description'] ?? null;
                            $item->qty2 = $qty;
                            $item->uprice = $uprice;
                            $item->commision = !empty($row['commission']) ? 1 : 0;
                            $item->tprice = $qty * $uprice;
                
                            $item->save();
                        }
                    }
                } catch (\Exception $e) {
                    dd('Error:', $e->getMessage());
                }
            });

            session()->flash('success', 'Invoice created successfully!');
            return redirect(route('invoice.manage'));

        }


    public function render()
    {
        return view('livewire.invoice.add', [
            'customers' => Customer::orderBy('c_name')->get(),
            'shippers' => Shipper::orderBy('c_name')->get(),
        ])->layout('layouts.app', ['title' => 'Add Invoice']);
    }
    public function getTotalProperty()
{
    return collect($this->items)->reduce(function ($carry, $item) {
        return $carry + (floatval($item['qty'] ?? 0) * floatval($item['unit_price'] ?? 0));
    }, 0);
}

public function getTotalCommissionProperty()
{
    return collect($this->items)->reduce(function ($carry, $item) {
        if (!empty($item['commission'])) {
            $lineTotal = floatval($item['qty'] ?? 0) * floatval($item['unit_price'] ?? 0);
            return $carry + ($lineTotal * (floatval($this->commission) / 100));
        }
        return $carry;
    }, 0);
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