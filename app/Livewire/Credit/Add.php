<?php

namespace App\Livewire\Credit;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\data_tb as Customer;
use App\Models\shipper_tb as Shipper;
use App\Models\credit_tb as Credit;
use App\Models\credit_items_tb as CreditItem;
use App\Models\order_tb as Order;
use App\Models\alerts_tb as Alert;
use App\Models\profile_tb as Profile;
use App\Models\profile_tb2 as ProfileDetail;

class Add extends Component
{
    public bool $showAlertPopup = false;
    public $alertMessages;

    public $newAlert = '';
    public $alertQuote = false;
    public $alertConfirmation = false;
    public $alertPacking = false;
    public $alertInvoice = false;
    public $alertCredit = false;

    public $editingAlertId = null;
    public $messageIds = [];

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

    public $customer = '';
    public $part_no = '';
    public $rev = '';
    public $oo = '';
    public $po = '';
    public $ord_by = '';
    public $lyrcnt = '';
    public $delto = '';
    public $date1 = '';
    public $stax = '';
    public $specialreq = '';
    public $alertHtml = '';

    public bool $showProfilePopup = false;
    public $profileMessages;

    public $items = [];

    public $search = '';
    public $matches = [];
    public $button_status = 0;


    protected function rules(): array
    {
        return [
            'vid' => ['required', 'exists:data_tb,data_id'],
            'sid' => ['required'],
            'namereq' => ['nullable', 'string', 'max:100'],
            'svia' => ['required', Rule::in(['Elecronic Data', 'Fedex', 'Personal Delivery', 'UPS', 'Other'])],
            'svia_oth' => ['nullable', 'required_if:svia,Other', 'string', 'max:50'],
            'fcharge' => ['nullable', 'numeric'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:50'],
            'sterms' => ['required', Rule::in(['Prepaid', 'Collect'])],
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
            'items' => ['array', 'size:6'],
            'items.*.item' => ['nullable', 'string', 'max:50'],
            'items.*.desc' => ['nullable', 'string'],
            'items.*.qty' => ['nullable', 'numeric'],
            'items.*.uprice' => ['nullable', 'numeric'],
        ];
    }

    public function mount(): void
    {
        $this->items = collect(range(1, 6))
            ->map(fn() => ['item' => '', 'desc' => '', 'qty' => null, 'uprice' => null])
            ->toArray();
        $this->alertTypes = is_array($this->alertTypes) ? $this->alertTypes : [];
    }

    public function getTotalProperty(): float
    {
        return collect($this->items)
            ->sum(fn($row) => (float) ($row['qty']) * (float) str_replace(',', '', $row['uprice']));
    }

    public function save(): void
    {
        $this->validate();
        $this->button_status = 1;
        $alerts = Alert::where('customer', $this->customer)
            ->where('part_no', $this->part_no)
            ->where('rev', $this->rev)
            ->where('atype', 'p')
            ->orderBy('id', 'desc')
            ->get()
            ->filter(function ($alert) {
                return in_array('cre', explode('|', $alert->viewable));
            });
        // for profile alert ..
        // Check for profile alerts
        $profiles = Profile::where('custid', $this->vid)->with('details')
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
            $this->processCreditSave();
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
            $this->processCreditSave();
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
                return in_array('cre', explode('|', $alert->viewable));
            });

        if ($alerts->count() > 0) {
            //$this->showAlertPopup = true;
            $this->alertMessages = $alerts;
        }
    }

    public function editAlert($id)
    {
        $alert = Alert::findOrFail($id);

        $this->editingAlertId = $id;
        $this->newAlert = $alert->alert;

        // Clear and reset alertTypes
        $this->alertTypes = [];
        $this->dispatch('alert-types-cleared'); // Helps with reactivity

        // Small delay to ensure Livewire processes the change
        usleep(100);

        // Set the actual values from the database
        $this->alertTypes = collect(explode('|', $alert->viewable))
            ->filter() // Remove empty values
            ->values()
            ->toArray();

        $this->dispatch('alert-types-updated'); // Notify frontend
    }


    public function updateAlert()
    {
        $this->validate(['newAlert' => 'required|string|max:255', 'alertTypes' => 'required|array|min:1']);
        // Debug: Log the current alertTypes
        logger()->debug('Updating Alert', [
            'editingAlertId' => $this->editingAlertId,
            'newAlert' => $this->newAlert,
            'alertTypes' => $this->alertTypes // Check this array
        ]);
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
        $this->reset(['newAlert', 'alertTypes']);
    }

    public function processCreditSave()
    {
        DB::transaction(function () {
            $credit = new Credit();
            $credit->fill([
                'vid' => $this->vid,
                'sid' => $this->sid,
                'namereq' => $this->namereq,
                'svia' => $this->svia,
                'fcharge' => $this->fcharge ?: 0,
                'city' => $this->city,
                'state' => $this->state,
                'sterm' => $this->sterms,
                'comments' => $this->comments,
                'podate' => now()->format('m/d/Y'),
                'customer' => $this->customer,
                'part_no' => $this->part_no,
                'rev' => $this->rev,
                'delto' => $this->delto,
                'ord_by' => $this->ord_by,
                'date1' => $this->date1,
                'dweek' => Carbon::parse($this->date1)->format('l'),
                'po' => $this->po,
                'our_ord_num' => $this->oo,
                'saletax' => $this->stax ?: 0,
                'no_layer' => $this->lyrcnt,
                'credit_date' => today()->format('Y-m-d'),
                'sp_reqs' => trim($this->specialreq),
                'svia_oth' => trim($this->svia_oth),
            ])->save();

            collect($this->items)->each(function ($row) use ($credit) {
                if (!empty($row['item'])) {
                    $qty = (float) $row['qty'];
                    $uprice = (float) str_replace(',', '', $row['uprice']);

                    $credit->items()->create([
                        'item' => $row['item'],
                        'itemdesc' => addslashes($row['desc']),
                        'qty2' => $qty,
                        'uprice' => $uprice,
                        'tprice' => $qty * $uprice,
                    ]);
                }
            });
        });

        session()->flash('success', 'Credit record added successfully.');
        return redirect(route('credit.manage'));
    }

    public function confirmSave()
    {
        $this->showAlertPopup = false;
        $this->processCreditSave();
    }

    public function lineTotal(int $i): float
    {
        $row = $this->items[$i] ?? ['qty' => 0, 'uprice' => 0];
        $qty = (float) ($row['qty'] ?? 0);
        $uprice = (float) str_replace(',', '', $row['uprice'] ?? 0);
        return $qty * $uprice;
    }

    public function render()
    {
        return view('livewire.credit.add', [
            'customers' => Customer::orderBy('c_name')->get(),
            'shippers' => Shipper::orderBy('c_name')->get(),
        ])->layout('layouts.app', ['title' => 'Add Credit']);
    }

    public function onKeyUp(string $value): void
    {
        $this->search = $value;

        if (mb_strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }

        $this->matches = Order::select('part_no', 'rev', 'cust_name')
            ->where('part_no', 'like', "%{$value}%")
            ->orWhere('cust_name', 'like', "%{$value}%")
            ->distinct()
            ->get()
            ->map(fn($row) => [
                'label' => "{$row->part_no}_{$row->rev}_{$row->cust_name}",
                'part' => $row->part_no,
                'rev' => $row->rev,
                'cust' => $row->cust_name,
            ])->toArray();
    }

    public function selectLookup(string $part, string $rev, string $cust): void
    {
        $this->search = "{$cust}_{$part}_{$rev}";
        $this->matches = [];

        $order = Order::where('part_no', $part)
            ->where('rev', $rev)
            ->where('cust_name', $cust)
            ->first();

        if ($order) {
            $this->customer = $order->cust_name;
            $this->rev = $order->rev;
            $this->part_no = $order->part_no;
            $this->lyrcnt = $order->no_layer;
            $this->ord_by = $order->req_by;
        }
    }

    public function useMatch(int $i): void
    {
        if (!isset($this->matches[$i]))
            return;
        $m = $this->matches[$i];
        $this->selectLookup($m['part'], $m['rev'], $m['cust']);
    }
}