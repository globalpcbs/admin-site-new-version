<?php

namespace App\Livewire\PackingSlips;

use Livewire\Component;
use App\Models\packing_tb;
use App\Models\packing_items_tb as items;
use App\Models\profile_tb3;
use App\Models\temp_profile2;
use App\Models\maincont_packing;
use App\Models\data_tb;
use App\Models\shipper_tb;
use App\Models\maincont_tb;
use Illuminate\Support\Facades\DB;
use App\Models\order_tb as Order;

class Edit extends Component
{
    public $packingId;
    public $vid, $sid, $namereq, $svia_oth, $fcharge, $city, $state, $sterms, $comments, $customer, $odate;
    public $svia, $saletax, $no_layer, $specialreqval, $commission;
    public $customer_look = '', $part_no = '', $rev = '', $oo = '', $po = '', $ord_by = '';
    public $lyrcnt = '', $delto = '', $date1 = '', $stax = '', $specialreq = '', $alertHtml = '';
    public $items = [], $maincontacts = [], $selectedMainContacts = [], $search = '', $matches = [];

    public function mount($id)
    {
        $this->packingId = $id;
        $packing = packing_tb::with(['items'])->findOrFail($id);

        $this->vid = $packing->vid;
        $this->sid = $packing->sid;
        $this->namereq = $packing->namereq;
        $this->svia = $packing->svia;
        $this->svia_oth = $packing->svia_oth;
        $this->fcharge = $packing->fcharge ?: null;
        $this->city = $packing->city;
        $this->state = $packing->state;
        $this->sterms = $packing->sterm;
        $this->comments = $packing->comments;
        $this->customer = $packing->customer;
        $this->part_no = $packing->part_no;
        $this->rev = $packing->rev;
        $this->delto = $packing->delto;
        $this->date1 = $packing->date1;
        $this->odate = $packing->odate;
        $this->po = $packing->po;
        $this->oo = $packing->our_ord_num;
        $this->saletax = $packing->saletax;
        $this->no_layer = $packing->no_layer;
        $this->specialreqval = $packing->sp_reqs;

        $this->items = $packing->items->map(function ($item) {
            return [
                'item' => $item->item,
                'desc' => $item->itemdesc,
                'qty' => $item->qty2,
                'shipqty' => $item->shipqty,
            ];
        })->toArray();

        $this->maincontacts = maincont_tb::where('coustid', $this->customer)->get();

        $this->selectedMainContacts = maincont_packing::where('packingid', $id)
            ->pluck('maincontid')
            ->toArray();
    }

    public function mainContact()
    {
        $this->maincontacts = maincont_tb::where('coustid', $this->customer)->get();
        $this->selectedMainContacts = [];
    }

    public function getTotalOrderedProperty()
    {
        return collect($this->items)->sum(fn ($item) => floatval(str_replace(',', '', $item['qty'] ?? 0)));
    }

    public function getTotalShippedProperty()
    {
        return collect($this->items)->sum(fn ($item) => floatval(str_replace(',', '', $item['shipqty'] ?? 0)));
    }

    public function update()
    {
        DB::beginTransaction();

        try {
            $packing = packing_tb::findOrFail($this->packingId);

            $packing->vid = $this->vid;
            $packing->sid = $this->sid;
            $packing->namereq = $this->namereq;
            $packing->svia = $this->svia;
            $packing->svia_oth = $this->svia === 'Other' ? $this->svia_oth : null;
            $packing->fcharge = $this->fcharge ?: null;
            $packing->city = $this->city;
            $packing->state = $this->state;
            $packing->sterm = $this->sterms;
            $packing->comments = $this->comments;
            $packing->customer = $this->customer;
            $packing->part_no = $this->part_no;
            $packing->rev = $this->rev;
            $packing->delto = $this->delto;
            $packing->date1 = $this->date1;
            $packing->odate = $this->odate;
            $packing->po = $this->po;
            $packing->our_ord_num = $this->oo;
            $packing->saletax = $this->saletax;
            $packing->no_layer = $this->no_layer;
            $packing->sp_reqs = $this->specialreqval;
            $packing->save();

            items::where('pid', $packing->invoice_id)->delete();
            foreach ($this->items as $row) {
                if (trim($row['item']) === '') continue;

                items::create([
                    'item' => $row['item'],
                    'itemdesc' => $row['desc'],
                    'qty2' => str_replace(',', '', $row['qty']),
                    'shipqty' => str_replace(',', '', $row['shipqty']),
                    'pid' => $packing->invoice_id,
                ]);
            }

            maincont_packing::where('packingid', $packing->invoice_id)->delete();
            foreach ($this->selectedMainContacts as $contactId) {
                maincont_packing::create([
                    'maincontid' => $contactId,
                    'packingid' => $packing->invoice_id,
                ]);
            }

            DB::commit();
            session()->flash('success', 'Packing Slip Updated Successfully!');
            return redirect()->route('packing.manage');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('warning', 'Update failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.packing-slips.edit', [
            'customers' => data_tb::orderBy('c_name')->get(),
            'shippers' => shipper_tb::orderBy('c_name')->get(),
            'totalOrdered' => $this->totalOrdered,
            'totalShipped' => $this->totalShipped,
        ])->layout('layouts.app', ['title' => 'Edit Packing Slip']);
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
         $this->customer_look = $order->cust_name;
         $this->rev = $order->rev;
         $this->part_no = $order->part_no;
         $this->lyrcnt = $order->no_layer;
         $this->ord_by = $order->req_by;
       //  dd($this->customer_look);
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