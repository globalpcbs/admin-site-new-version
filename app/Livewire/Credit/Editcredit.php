<?php

namespace App\Livewire\Credit;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\data_tb      as Customer;
use App\Models\shipper_tb   as Shipper;
use App\Models\credit_tb    as Credit;
use App\Models\order_tb     as Order;
use App\Models\alerts_tb as Alert;
use App\Models\profile_tb as Profile;
use App\Models\profile_tb2 as ProfileDetail;

class EditCredit extends Component
{
    /* ——— primary-key of the record we are editing ——— */
    public int $creditId;

    /* ——— every public prop copied 1-for-1 from Add ——— */
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

    public $items = [];          // 6 rows
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
    public $alertTypes = [];
    public $button_status = 0;


    /* ——— identical rules from Add ——— */
    protected function rules(): array
    {
        return [
            'vid'               => ['required', 'exists:data_tb,data_id'],
            'sid'               => ['required'],
            'namereq'           => ['nullable', 'string', 'max:100'],
            'svia'              => ['required', Rule::in(['Elecronic Data','Fedex','Personal Delivery','UPS','Other'])],
            'svia_oth'          => ['nullable','required_if:svia,Other','string','max:50'],
            'fcharge'           => ['nullable','numeric'],
            'city'              => ['nullable','string','max:100'],
            'state'             => ['nullable','string','max:50'],
            'sterms'            => ['required', Rule::in(['Prepaid','Collect'])],
            'comments'          => ['nullable','string'],
            'customer'          => ['nullable','string','max:100'],
            'part_no'           => ['nullable','string','max:50'],
            'rev'               => ['nullable','string','max:20'],
            'oo'                => ['nullable','string','max:50'],
            'po'                => ['nullable','string','max:50'],
            'ord_by'            => ['nullable','string','max:50'],
            'lyrcnt'            => ['nullable','string'],
            'delto'             => ['nullable','string','max:100'],
            'date1'             => ['nullable','date_format:Y-m-d'],
            'stax'              => ['nullable','numeric'],
            'items'             => ['array','size:6'],
            'items.*.item'      => ['nullable','string','max:50'],
            'items.*.desc'      => ['nullable','string'],
            'items.*.qty'       => ['nullable','numeric'],
            'items.*.uprice'    => ['nullable','numeric'],
        ];
    }

    /* ——— mount($id) loads the record into props ——— */
    public function mount(int $credit): void
    {
        $this->creditId = $credit;

        $model = Credit::with('items')->findOrFail($credit);

        /* main fields */
        foreach ($model->getAttributes() as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }

        /* six item rows (pad with blanks) */
        $this->items = collect(range(0,5))->map(function ($i) use ($model) {
            $row = $model->items[$i] ?? null;
            return [
                'item'   => $row->item     ?? '',
                'desc'   => $row->itemdesc ?? '',
                'qty'    => $row->qty2     ?? null,
                'uprice' => $row->uprice   ?? null,
            ];
        })->toArray();
    }

    /* ——— computed grand total (same as Add) ——— */
    public function getTotalProperty(): float
    {
        return collect($this->items)->sum(fn ($r) =>
            (float) $r['qty'] * (float) str_replace(',','',$r['uprice'])
        );
    }
    public function update(): void
{
    $this->validate();
    $this->button_status = 1;
    
    // Check for alerts
    $alerts = Alert::where('customer', $this->customer)
        ->where('part_no', $this->part_no)
        ->where('rev', $this->rev)
        ->where('atype', 'p')
        ->orderBy('id', 'desc')
        ->get()
        ->filter(function ($alert) {
            return in_array('cre', explode('|', $alert->viewable));
        });

    // Check for profile alerts
    $profiles = Profile::where('custid', $this->vid)
        ->with('details')
        ->get();

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

    // If no alerts, proceed with update
    if (!$hasAlerts && !$hasProfiles) {
        $this->processCreditUpdate();
    }
}

public function closeAlertPopup(): void
{
    $this->showAlertPopup = false;
    $this->checkIfShouldSave();
}

public function closeProfilePopup(): void
{
    $this->showProfilePopup = false;
    $this->checkIfShouldSave();
}

protected function checkIfShouldSave(): void
{
    if (!$this->showAlertPopup && !$this->showProfilePopup) {
        $this->processCreditUpdate();
    }
}

public function addAlert(): void
{
    $this->validate([
        'newAlert' => 'required|string|max:255',
        'alertTypes' => 'required|array|min:1'
    ]);

    Alert::create([
        'customer' => $this->customer ?? '',
        'part_no' => $this->part_no ?? '',
        'rev' => $this->rev ?? '',
        'alert' => trim($this->newAlert),
        'viewable' => collect($this->alertTypes)->implode('|'),
        'atype' => 'p',
    ]);

    $this->reset(['newAlert', 'alertTypes']);
    $this->loadAlerts();
}

public function editAlert($id): void
{
    $alert = Alert::findOrFail($id);
    $this->editingAlertId = $id;
    $this->newAlert = $alert->alert;
    $this->alertTypes = explode('|', $alert->viewable);
}

public function updateAlert(): void
{
    $this->validate(['newAlert' => 'required|string|max:255']);
    
    Alert::where('id', $this->editingAlertId)->update([
        'alert' => trim($this->newAlert),
        'viewable' => collect($this->alertTypes)->implode('|'),
    ]);

    $this->reset(['newAlert', 'alertTypes', 'editingAlertId']);
    $this->loadAlerts();
}

public function deleteAlert($id): void
{
    Alert::where('id', $id)->delete();
    $this->loadAlerts();
}

public function loadAlerts(): void
{
    $this->alertMessages = Alert::where('customer', $this->customer)
        ->where('part_no', $this->part_no)
        ->where('rev', $this->rev)
        ->where('atype', 'p')
        ->orderBy('id', 'desc')
        ->get()
        ->filter(function ($alert) {
            return in_array('cre', explode('|', $alert->viewable));
        });
}

protected function processCreditUpdate()
{
           $this->validate();

        DB::transaction(function () {

            /* 1. update the credit row */
            $credit = Credit::findOrFail($this->creditId);
           // dd($credit);
            $credit->fill([
                'vid'        => $this->vid,
                'sid'        => $this->sid,
                'namereq'    => $this->namereq,
                'svia'       => $this->svia,
                'fcharge'    => $this->fcharge ?: 0,
                'city'       => $this->city,
                'state'      => $this->state,
                'sterm'      => $this->sterms,
                'comments'   => $this->comments,
                'customer'   => $this->customer,
                'part_no'    => $this->part_no,
                'rev'        => $this->rev,
                'delto'      => $this->delto,
                'ord_by'     => $this->ord_by,
                'date1'      => $this->date1,
                'dweek'      => Carbon::parse($this->date1)->format('l'),
                'po'         => $this->po,
                'our_ord_num'=> $this->oo,
                'saletax'    => $this->stax ?: 0,
                'no_layer'   => $this->lyrcnt,
                'sp_reqs'    => trim($this->specialreq),
                'svia_oth'   => trim($this->svia_oth),
            ])->save();

            /* 2. rebuild items */
            $credit->items()->delete();

            collect($this->items)->each(function ($row) use ($credit) {
                if (!empty($row['item'])) {
                    $qty    = (float) $row['qty'];
                    $uprice = (float) str_replace(',','',$row['uprice']);

                    $credit->items()->create([
                        'item'     => $row['item'],
                        'itemdesc' => addslashes($row['desc']),
                        'qty2'     => $qty,
                        'uprice'   => $uprice,
                        'tprice'   => $qty * $uprice,
                    ]);
                }
            });
        });

        session()->flash('success', 'Credit record updated.');
        return redirect()->route('credit.manage');
}


    /* ——— live helpers (lineTotal, search, etc.) ——— */
    public function lineTotal(int $i): float
    {
        $row = $this->items[$i] ?? ['qty' => 0, 'uprice' => 0];
        return (float) $row['qty'] * (float) str_replace(',','',$row['uprice']);
    }

    public function onKeyUp(string $value): void
    {
        $this->search = $value;
        if (mb_strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }

        $this->matches = Order::query()
            ->select('part_no','rev','cust_name')
            ->where('part_no','like',"%{$value}%")
            ->orWhere('cust_name','like',"%{$value}%")
            ->distinct()
            ->get()
            ->map(fn ($r) => [
                'label' => "{$r->part_no}_{$r->rev}_{$r->cust_name}",
                'part'  => $r->part_no,
                'rev'   => $r->rev,
                'cust'  => $r->cust_name,
            ])->toArray();
    }

    public function selectLookup(string $part, string $rev, string $cust): void
    {
        $this->search   = "{$cust}_{$part}_{$rev}";
        $this->matches  = [];

        $order = Order::where('part_no',$part)
                      ->where('rev',$rev)
                      ->where('cust_name',$cust)
                      ->first();

        if ($order) {
            $this->customer = $order->cust_name;
            $this->rev      = $order->rev;
            $this->part_no  = $order->part_no;
            $this->lyrcnt   = $order->no_layer;
            $this->ord_by   = $order->req_by;
        }
    }

    public function useMatch(int $i): void
    {
        if (isset($this->matches[$i])) {
            $m = $this->matches[$i];
            $this->selectLookup($m['part'],$m['rev'],$m['cust']);
        }
    }

    /* ——— view ——— */
    public function render()
    {
        return view('livewire.credit.editcredit',[
            'customers' => Customer::orderBy('c_name')->get(),
            'shippers'  => Shipper::orderBy('c_name')->get(),
        ])->layout('layouts.app',['title'=>'Edit Credit']);
    }
}