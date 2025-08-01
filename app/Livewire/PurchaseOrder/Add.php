<?php

namespace App\Livewire\PurchaseOrder;

use Livewire\Component;
use App\Models\data_tb;
use App\Models\shipper_tb;
use App\Models\vendor_tb;
use App\Models\porder_tb;
use App\Models\items_tb;
use App\Models\order_tb     as Order;
use Illuminate\Support\Facades\DB;

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
    public function save()
    {
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
            'customers' => data_tb::where('c_name', '!=', '')->orderBy('c_name')->get(),
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