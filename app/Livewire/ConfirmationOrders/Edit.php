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

class Edit extends Component
{
    public $corder;

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
    public $customer;
    public $part_no;
    public $rev;
    public $oo;
    public $po;
    public $ord_by;
    public $lyrcnt;
    public $date1;
    public $stax;
    public $specialreq;
    public $alertHtml;
    public $totalPrice;
    public $specialreqval;
    public $comments;
    public $date2;
    public $mdl;
    public $items = [];
    public $deliveries = [];

    public $search = '';
    public $matches = [];

    public function mount($id)
    {
        $this->corder = corder_tb::findOrFail($id);

        $this->fill($this->corder->only([
            'vid','sid','namereq','delto','svia','svia_oth','city','state','sterms','rohs','customer',
            'part_no','rev','oo','po','ord_by','lyrcnt','date1','stax','specialreq','specialreqval','comments',
            'date2','mdl'
        ]));

        $this->items = citems_tb::where('pid', $this->corder->poid)
            ->take(6)
            ->get(['item', 'itemdesc', 'qty2 as qty', 'uprice'])
            ->toArray();

        $this->items = array_pad($this->items, 6, ['item'=>'','itemdesc'=>'','qty'=>'','uprice'=>'']);

        $this->deliveries = mdlitems_tb::where('pid', $this->corder->poid)
            ->get(['qty','date'])
            ->toArray();

        $this->deliveries = array_pad($this->deliveries, 12, ['qty' => '', 'date' => '']);
    }

    public function update()
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
            $this->corder->update([
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

            citems_tb::where('pid', $this->corder->poid)->delete();
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
                        'pid' => $this->corder->poid,
                    ]);
                }
            }

            mdlitems_tb::where('pid', $this->corder->poid)->delete();
            foreach ($this->deliveries as $delivery) {
                if (!empty($delivery['qty'])) {
                    mdlitems_tb::create([
                        'qty' => $delivery['qty'],
                        'date' => $delivery['date'],
                        'pid' => $this->corder->poid,
                    ]);
                }
            }

            DB::commit();
            session()->flash('success', 'Confirmation order updated successfully.');
            return redirect(route('confirmation.manage'));

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('warning', 'Error: ' . $e->getMessage());
        }
    }

    public function getTotalProperty(): float
    {
        return collect($this->items)
            ->sum(fn($row)=>(float)$row['qty'] * (float)str_replace(',','',$row['uprice']));
    }

    public function lineTotal(int $i): float
    {
        $row = $this->items[$i] ?? ['qty' => 0, 'uprice' => 0];
        $qty = (float) ($row['qty'] ?? 0);
        $uprice = (float) str_replace(',', '', $row['uprice'] ?? 0);
        return $qty * $uprice;
    }

    public function onKeyUp(string $value): void
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
            ->distinct()
            ->get()
            ->map(fn ($row) => [
                'label' => "{$row->part_no}_{$row->rev}_{$row->cust_name}",
                'part'  => $row->part_no,
                'rev'   => $row->rev,
                'cust'  => $row->cust_name,
            ])->toArray();
    }

    public function selectLookup(string $part, string $rev, string $cust): void
    {
        $this->search = "{$cust}_{$part}_{$rev}";
        $this->matches = [];

        $order = Order::where('part_no',  $part)
            ->where('rev',      $rev)
            ->where('cust_name',$cust)
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
        if (! isset($this->matches[$i])) return;
        $m = $this->matches[$i];
        $this->selectLookup($m['part'], $m['rev'], $m['cust']);
    }

    public function render()
    {
        return view('livewire.confirmation-orders.edit', [
            'customers' => Customer::orderBy('c_name')->get(),
            'shippers' => Shipper::orderBy('c_name')->get(),
        ])->layout('layouts.app', ['title' => 'Edit Confirmation Order']);
    }
}