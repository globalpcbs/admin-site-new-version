<?php 

namespace App\Livewire\ConfirmationOrders;

use Livewire\Component;
use App\Models\corder_tb;
use App\Models\citems_tb;
use App\Models\data_tb      as Customer;
use App\Models\shipper_tb   as Shipper;
use App\Models\mdlitems_tb;
use App\Models\order_tb     as Order;
use Illuminate\Support\Facades\DB;

class Add extends Component
{
    // Fields (separate properties)
    public $vid;
    public $sid;
    public $namereq;
    public $delto;
    public $svia;
    public $svia_oth;
    public $city;
    public $state;
    public $sterms;
    public $rohs;
      // Lookup / line‑item‑related
      public $customer     = '';
      public $part_no      = '';
      public $rev          = '';
      public $oo           = '';    // our PO
      public $po           = '';    // customer PO
      public $ord_by       = '';
      public $lyrcnt       = '';
      public $date1        = '';    // delivered‑on (string, Y‑m‑d)
      public $stax         = '';
      public $specialreq   = '';    // value actually stored
      public $alertHtml    = '';    // rendered HTML shown to user
      public $totalPrice;
    public $specialreqval;
    public $comments = '';
    public $date2;
    public $mdl = '';
    public $items = [];
    public $deliveries = [];
    /* ─── existing public props … ───────────────────────────────────── */
    public $search     = '';          // what the user is typing
    public $matches    = [];          // array of suggestions ⬅️  NEW

    public function mount()
    {
         // six blank item rows
         $this->items = collect(range(1,6))
         ->map(fn()=>['item'=>'','itemdesc'=>'','qty'=>null,'uprice'=>null])
         ->toArray();

        $this->deliveries = array_fill(0, 12, [
            'qty' => '', 'date' => now()->format('l-m-d-Y'),
        ]);
    }

    public function save()
    {
        $this->validate([
            'vid' => 'required|integer',
            'sid' => 'required',
            'namereq' => 'nullable|string',
            'svia' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'sterms' => 'nullable|string',
            'rohs' => 'nullable|string',
            'comments' => 'nullable|string',
            'customer' => 'nullable|string',
            'part_no' => 'nullable|string',
            'rev' => 'nullable|string',
            'date1' => 'nullable|string',
            'date2' => 'nullable|string',
            'po' => 'nullable|string',
            'oo' => 'nullable|string',
            'mdl' => 'nullable|string',
            'delto' => 'nullable|string',
            'stax' => 'nullable|string',
            'lyrcnt' => 'nullable|string',
            'specialreqval' => 'nullable|string',
            'svia_oth' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $corder = new corder_tb();
            $corder->fill([
                'vid' => $this->vid,
                'sid' => $this->sid,
                'namereq' => $this->namereq,
                'svia' => $this->svia,
                'svia_oth' => trim($this->svia_oth),
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
                'po' => $this->po,
                'our_ord_num' => $this->oo,
                'mdl' => $this->mdl,
                'delto' => $this->delto,
                'stax' => $this->stax,
                'no_layer' => $this->lyrcnt,
                'sp_reqs' => trim($this->specialreqval),
                'dweek' => substr($this->date1 ?? '', 11),
            ]);
            $corder->save();

            $pid = $corder->poid;

            foreach ($this->items as $item) {
                if (!empty($item['item'])) {
                    $qty = floatval(str_replace(',', '', $item['qty']));
                    $uprice = floatval(str_replace(',', '', $item['uprice']));
                    $tprice = $qty * $uprice;

                    citems_tb::create([
                        'item' => $item['item'],
                        'itemdesc' => addslashes($item['itemdesc']),
                        'qty2' => $qty,
                        'uprice' => $uprice,
                        'tprice' => number_format($tprice, 2, '.', ''),
                        'pid' => $pid,
                    ]);
                }
            }

            foreach ($this->deliveries as $delivery) {
                if (!empty($delivery['qty'])) {
                    mdlitems_tb::create([
                        'qty' => $delivery['qty'],
                        'date' => $delivery['date'],
                        'pid' => $pid,
                    ]);
                }
            }

            DB::commit();
            session()->flash('success', 'Confirmation order added successfully.');
            return redirect(route('confirmation.manage'));
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('warning', 'Error: ' . $e->getMessage());
        }
    }
       /* ─── Computed helpers ─────────────────────────────────────────────── */
    public function getTotalProperty(): float
    {
        return collect($this->items)
            ->sum(fn($row)=>
                (float)$row['qty'] * (float)str_replace(',','',$row['uprice'])
            );
    }
    public function lineTotal(int $i): float
    {
        $row = $this->items[$i] ?? ['qty' => 0, 'uprice' => 0];

        $qty    = (float) ($row['qty']    ?? 0);
        $uprice = (float) str_replace(',', '', $row['uprice'] ?? 0);

        return $qty * $uprice;
    }
    public function render()
    {
        return view('livewire.confirmation-orders.add',[
            'customers' => Customer::orderBy('c_name')->get(),
            'shippers'  => Shipper::orderBy('c_name')->get(),
        ])
            ->layout('layouts.app', ['title' => 'Add Confirmation Order']);
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
         //dd($order);
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