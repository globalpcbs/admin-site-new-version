<?php

namespace App\Livewire\PurchaseOrder;

use Livewire\Component;
use App\Models\data_tb;
use App\Models\shipper_tb;
use App\Models\vendor_tb;
use App\Models\porder_tb;
use App\Models\items_tb;
use App\Models\order_tb as Order;

class Edit extends Component
{
    public $poid;

    public $vid, $sid, $namereq, $namereq1, $svia, $svia_oth, $city, $state;
    public $sterms, $rohs, $comments, $podate, $customer, $part_no, $rev;
    public $date1, $date2, $cpo, $dweek, $no_layer, $cancharge, $ordon, $iscancel, $ccharge;
    public $specialreqval;

    public $items = [];
    public $total = 0;

    public $search = '';
    public $matches = [];
    public $inputKey;

    public function mount($id)
    {
        $po = porder_tb::findOrFail($id);
        $this->poid = $po->poid;

        $this->vid = $po->vid;
        $this->sid = $po->sid;
        $this->namereq = $po->namereq;
        $this->namereq1 = $po->namereq1;
        $this->svia = $po->svia;
        $this->svia_oth = $po->svia_oth;
        $this->city = $po->city;
        $this->state = $po->state;
        $this->sterms = $po->sterms;
        $this->rohs = $po->rohs;
        $this->comments = $po->comments;
        $this->podate = $po->podate;
        $this->customer = $po->customer;
        $this->part_no = $po->part_no;
        $this->rev = $po->rev;
        $this->date1 = $po->date1;
        $this->date2 = $po->date2;
        $this->cpo = $po->po;
        $this->dweek = $po->dweek;
        $this->no_layer = $po->no_layer;
        $this->cancharge = $po->cancharge;
        $this->ordon = $po->ordon;
        $this->iscancel = $po->iscancel;
        $this->ccharge = $po->ccharge;
        $this->specialreqval = $po->sp_reqs;

        $this->items = items_tb::where('pid', $po->poid)
            ->get()
            ->map(function ($item) {
                return [
                    'item' => $item->item,
                    'desc' => $item->itemdesc,
                    'dpdesc' => $item->dpval,
                    'qty' => $item->qty2,
                    'uprice' => $item->uprice,
                ];
            })
            ->toArray();

        // Fill up to 6 rows
        while (count($this->items) < 6) {
            $this->items[] = ['item' => '', 'desc' => '', 'dpdesc' => '', 'qty' => '', 'uprice' => ''];
        }

        $this->recalculateTotal();
        $this->inputKey = uniqid();
    }

    public function updatedItems($value, $key)
    {
        $this->recalculateTotal();
    }

    public function recalculateTotal()
    {
        $this->total = collect($this->items)->sum(function ($item) {
            return (float) ($item['qty'] ?? 0) * (float) ($item['uprice'] ?? 0);
        });
    }

    public function update()
    {
        $po = porder_tb::findOrFail($this->poid);

        $po->update([
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
            'customer' => $this->customer,
            'part_no' => $this->part_no,
            'rev' => $this->rev,
            'date1' => $this->date1,
            'date2' => $this->date2,
            'po' => $this->cpo,
            'dweek' => substr($this->date1, 11),
            'no_layer' => $this->no_layer,
            'cancharge' => $this->cancharge,
            'ordon' => $this->ordon,
            'iscancel' => $this->iscancel,
            'ccharge' => $this->ccharge,
            'sp_reqs' => $this->specialreqval,
        ]);

        items_tb::where('pid', $this->poid)->delete();

        foreach ($this->items as $item) {
            if (!empty($item['item'])) {
                $qty = floatval($item['qty']);
                $uprice = floatval($item['uprice']);
                $tprice = $qty * $uprice;

                items_tb::create([
                    'pid' => $this->poid,
                    'item' => $item['item'],
                    'itemdesc' => $item['desc'],
                    'qty2' => $qty,
                    'uprice' => $uprice,
                    'tprice' => $tprice,
                    'dpval' => $item['dpdesc'],
                ]);
            }
        }

        session()->flash('success', 'Purchase order updated.');
        return redirect()->route('purchase.orders.manage');
    }

    public function onKeyUp(string $value)
    {
        $this->search = $value;

        if (strlen(trim($value)) < 2) {
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
            $this->inputKey = uniqid();
        }
    }

    public function render()
    {
        return view('livewire.purchase-order.edit', [
            'vendors' => vendor_tb::orderBy('c_name')->get(),
            'shippers' => shipper_tb::orderBy('c_name')->get(),
            'customers' => data_tb::where('c_name', '!=', '')->orderBy('c_name')->get(),
        ])->layout('layouts.app');
    }
}