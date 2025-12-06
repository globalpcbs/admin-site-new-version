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
    public $button_status = 0;
    public array $alertTypes = [];


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
        
        // Check if there are any pending alerts/profiles from session
        if (session('invoice_alerts')) {
            $this->showAlertPopup = true;
            $this->alertMessages = session('invoice_alerts');
        }
        
        if (session('invoice_profiles')) {
            $this->showProfilePopup = true;
            $this->profileMessages = session('invoice_profiles');
        }
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
        // First validate the form
        $this->validate([
            'vid' => ['required', 'exists:data_tb,data_id'],
            'sid' => ['required'],
            // Add other validation rules as needed
        ]);

        $this->button_status = 1;

        // Check for alerts
        $alerts = Alert::where('customer', $this->customer)
            ->where('part_no', $this->part_no)
            ->where('rev', $this->rev)
            ->where('atype', 'p')
            ->orderBy('id', 'desc')
            ->get()
            ->filter(function ($alert) {
                return in_array('inv', explode('|', $alert->viewable));
            });

        // Check for profile requirements
        $profiles = Profile::where('custid', $this->vid)
            ->whereHas('details', function ($query) {
                $query->where('viewable', 'LIKE', '%cre%');
            })
            ->with(['details' => function ($query) {
                $query->where('viewable', 'LIKE', '%cre%');
            }])
            ->get();

        $hasAlerts = $alerts->count() > 0;
        $hasProfiles = $profiles->count() > 0;

        // Store alerts/profiles in session to persist across redirects if needed
        session(['invoice_alerts' => $alerts]);
        session(['invoice_profiles' => $profiles]);

        // Show popups if they exist
        if ($hasAlerts) {
            $this->showAlertPopup = true;
            $this->alertMessages = $alerts;
            
            // Dispatch Livewire event to ensure modal opens
            $this->dispatch('show-alert-popup');
        }

        if ($hasProfiles) {
            $this->showProfilePopup = true;
            $this->profileMessages = $profiles;
            
            // Dispatch Livewire event to ensure modal opens
            $this->dispatch('show-profile-popup');
        }

        // If no popups at all, save immediately
        if (!$hasAlerts && !$hasProfiles) {
            $this->saveproccess();
        }
    }
    
    public function closeAlertPopup(): void
    {
        $this->showAlertPopup = false;
        // Clear the session data
        session()->forget('invoice_alerts');
        $this->checkIfShouldSave();
    }

    public function closeProfilePopup(): void
    {
        $this->showProfilePopup = false;
        // Clear the session data
        session()->forget('invoice_profiles');
        $this->checkIfShouldSave();
    }

    protected function checkIfShouldSave(): void
    {
        // Only save if both popups are closed AND we're not in the middle of adding/editing alerts
        if (!$this->showAlertPopup && !$this->showProfilePopup && !$this->editingAlertId && empty($this->newAlert)) {
            // Double-check that all required validations passed
            try {
                $this->saveproccess();
            } catch (\Exception $e) {
                $this->button_status = 0;
                session()->flash('error', 'Failed to save invoice: ' . $e->getMessage());
            }
        }
    }

    public function addAlert(): void
    {
        $this->validate([
            'newAlert' => 'required|string|max:255',
            'alertTypes' => 'required|array|min:1'
        ]);

        try {
            $alert = Alert::create([
                'customer' => $this->customer ?? '',
                'part_no' => $this->part_no ?? '',
                'rev' => $this->rev ?? '',
                'alert' => trim($this->newAlert),
                'viewable' => collect($this->alertTypes)->implode('|'),
                'atype' => 'p',
            ]);

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
        $this->dispatch('alert-types-updated');
    }

    public function updateAlert()
    {
        $this->validate(['newAlert' => 'required|string|max:255']);
        
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
                foreach ($this->items as $index => $row) {
                    if (!empty($row['item'])) {
                        $qty = (float) ($row['qty'] ?? 0);
                        $uprice = (float) str_replace(',', '', $row['unit_price'] ?? 0);
            
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
            } catch (\Exception $e) {
                throw new \Exception('Error saving line items: ' . $e->getMessage());
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
    
    public function clearMatches(): void
    {
        $this->matches = [];
    }
    
    public function selectLookup(string $part, string $rev, string $cust): void
    {
        // First clear the search and matches
        $this->search = "{$cust}_{$part}_{$rev}";
        $this->matches = [];
        
        // Find the order record
        $order = Order::where('part_no', $part)
            ->where('rev', $rev)
            ->where('cust_name', $cust)
            ->first();
        
        if ($order) {
            // Populate all the fields
            $this->customer = $order->cust_name ?? '';
            $this->rev = $order->rev ?? '';
            $this->part_no = $order->part_no ?? '';
            $this->lyrcnt = $order->no_layer ?? '';
            $this->ord_by = $order->req_by ?? '';
            $this->oo = $order->our_ord_num ?? '';
            $this->po = $order->po ?? '';
            $this->delto = $order->delivered_to ?? $order->deliver_to ?? '';
            
            // If you have a date field in orders, populate it
            if (isset($order->date1) || isset($order->delivered_on)) {
                $this->date1 = $order->date1 ?? $order->delivered_on ?? '';
            }
            
            // Dispatch event to notify the view
            $this->dispatch('lookup-completed');
        }
    }
     
    public function useMatch(int $i): void
    {
        if (! isset($this->matches[$i])) {
            return;
        }

        $m = $this->matches[$i];
        $this->selectLookup($m['part'], $m['rev'], $m['cust']);
    }
}