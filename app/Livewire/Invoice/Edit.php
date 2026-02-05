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
    public array $alertTypes = [];



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
            'qty' => 0,  // Changed from null to 0
            'unit_price' => 0,  // Changed from null to 0
            'commission' => false,
        ]);

        $this->reps = reps::all();
        $this->calculateTotals();
        
        // Check if there are any pending alerts/profiles from session
        if (session('invoice_edit_alerts')) {
            $this->showAlertPopup = true;
            $this->alertMessages = session('invoice_edit_alerts');
        }
        
        if (session('invoice_edit_profiles')) {
            $this->showProfilePopup = true;
            $this->profileMessages = session('invoice_edit_profiles');
        }
    }

    // Add these lifecycle methods for real-time calculations
    public function boot()
    {
        $this->calculateTotals();
    }
    
    public function hydrate()
    {
        $this->calculateTotals();
    }
    
    // Handle immediate updates when typing
    public function updating($name, $value)
    {
        if (str_starts_with($name, 'items.')) {
            $this->calculateTotals();
        }
    }
    
    public function updated($property)
    {
        if (str_starts_with($property, 'items.') || $property === 'commission') {
            $this->calculateTotals();
        }
    }
    
    // Helper method to safely convert to float
    private function safeFloat($value): float
    {
        if (is_null($value) || $value === '' || $value === false) {
            return 0.0;
        }
        
        // Remove any commas and non-numeric characters except decimal point and minus
        $cleaned = preg_replace('/[^0-9\.\-]/', '', (string)$value);
        
        return floatval($cleaned);
    }

    public function calculateTotals()
    {
        $this->total = 0;
        $this->totalCommission = 0;

        foreach ($this->items as $row) {
            $qty = $this->safeFloat($row['qty'] ?? 0);
            $price = $this->safeFloat($row['unit_price'] ?? 0);
            $lineTotal = $qty * $price;

            $this->total += $lineTotal;
            
            // Check if commission is checked and has valid values
            if (($row['commission'] ?? false) && $qty > 0 && $price > 0) {
                $commissionPercent = $this->safeFloat($this->commission ?? 0);
                $commissionAmount = $lineTotal * ($commissionPercent / 100);
                $this->totalCommission += $commissionAmount;
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
            
        // Check for profile alerts
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
        session(['invoice_edit_alerts' => $alerts]);
        session(['invoice_edit_profiles' => $profiles]);

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
        session()->forget('invoice_edit_alerts');
        $this->checkIfShouldSave();
    }

    public function closeProfilePopup(): void
    {
        $this->showProfilePopup = false;
        // Clear the session data
        session()->forget('invoice_edit_profiles');
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
                session()->flash('error', 'Failed to update invoice: ' . $e->getMessage());
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

    public function saveproccess()
    {
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
            
            InvoiceItem::where('pid', $invoice->invoice_id)->delete();
            foreach ($this->items as $row) {
                if (!empty($row['item'])) {
                    $qty = $this->safeFloat($row['qty']);
                    $uprice = $this->safeFloat($row['unit_price']);

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
        // Ensure totals are calculated before rendering
        $this->calculateTotals();
        return view('livewire.invoice.edit', [
            'customers' => Customer::orderBy('c_name')->get(),
            'shippers' => Shipper::orderBy('c_name')->get(),
        ])->layout('layouts.app', ['title' => 'Edit Invoice']);
    }
    
    public function lineTotal($index)
    {
        if (!isset($this->items[$index])) {
            return 0;
        }
        
        $row = $this->items[$index];
        $qty = $this->safeFloat($row['qty'] ?? 0);
        $unit = $this->safeFloat($row['unit_price'] ?? 0);
        
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
    
    public function clearMatches(): void
    {
        $this->matches = [];
    }
    
    /* user clicked a suggestion */
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
    
    /** Handle the click coming from <li wire:click="useMatch($i)"> */
    public function useMatch(int $i): void
    {
        if (! isset($this->matches[$i])) {
            return;
        }

        $m = $this->matches[$i];
        $this->selectLookup($m['part'], $m['rev'], $m['cust']);
    }
    
    // Computed property for total (optional, but good for consistency)
    public function getTotalProperty()
    {
        return collect($this->items)->reduce(function ($carry, $item) {
            $qty = $this->safeFloat($item['qty'] ?? 0);
            $price = $this->safeFloat($item['unit_price'] ?? 0);
            return $carry + ($qty * $price);
        }, 0);
    }
}