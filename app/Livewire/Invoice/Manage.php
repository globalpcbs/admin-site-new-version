<?php

namespace App\Livewire\Invoice;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\invoice_tb as Invoice;
use App\Models\invoice_items_tb as InvoiceItem;

class Manage extends Component
{
    use WithPagination;

    public $perPage = 100;
    public $searchPart = '';
    public $searchCustomer = '';

    public $showPaymentModal = false;
    public $selectedInvoiceId;
    public $paytype = '';
    public $paydetail = '';
    public $paydate = '';
    public $paynote = '';
    // 
    // public $mailstop = 0;
    // public $pending  = 0;
      // for search ..
    public $partSearchInput = '';
    public $customerSearchInput = '';
    public $searchPartNo = '';
    
    public function updatingSearchPart() { $this->resetPage(); }
    public function updatingSearchCustomer() { $this->resetPage(); }

    public function openPaymentModal($id)
    {
        $invoice = Invoice::findOrFail($id);
      //  dd($invoice->invoice_id);
        $this->selectedInvoiceId = $invoice->invoice_id;
        $invoice->ispaid = 1;
        $invoice->save();
        // $this->paytype = $invoice->paytype ?? '';
        // $this->paydetail = $invoice->paydetail ?? '';
        // $this->paydate = $invoice->paydate ?? '';
        // $this->paynote = $invoice->paynote ?? '';
        $this->showPaymentModal = true;
    }

    public function savePayment()
    {
        $invoice = Invoice::findOrFail($this->selectedInvoiceId);
        $invoice->paytype = $this->paytype;
        $invoice->paydetail = $this->paydetail;
        $invoice->paydate = $this->paydate;
        $invoice->paynote = $this->paynote;
        $invoice->save();

        $this->reset([
            'showPaymentModal', 'selectedInvoiceId', 'paytype',
            'paydetail', 'paydate', 'paynote'
        ]);
        session()->flash('success', 'Payment Details Updated Successfully!');
    }

    public function togglePaid($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->ispaid = $invoice->ispaid == 1 ? 0 : 1;
        $invoice->save();
        
    }
    public function togglePending($id){
        $invoice = Invoice::findOrFail($id);
        $invoice->pending = $invoice->pending == 1 ? 0 : 1;
        $invoice->save();
        session()->flash('success', 'Past Due Updated Successfully!');
    }
    public function toggleMailStop($id){
        $invoice = Invoice::findOrFail($id);
        $invoice->mailstop = $invoice->mailstop == 1 ? 0 : 1;
        $invoice->save();
        session()->flash('success', 'Mail Stop Updated Successfully!');

    }
    public function delete($id)
    {
        Invoice::findOrFail($id)->delete();
    }

    public function duplicate($id)
    {
        $original = Invoice::findOrFail($id);
        $copy = $original->replicate();
        $copy->pending = 0;
        $copy->ispaid = 0;
        $copy->mailstop = 0;
        $copy->podate = now()->format('m/d/Y');
        $copy->save();

        $newId = $copy->id;

        $items = InvoiceItem::where('pid', $id)->get();
        foreach ($items as $item) {
            InvoiceItem::create([
                'item' => $item->item,
                'itemdesc' => $item->itemdesc,
                'qty2' => $item->qty2,
                'uprice' => $item->uprice,
                'tprice' => $item->tprice,
                'pid' => $newId
            ]);
        }
        session()->flash('success', 'Invoice Duplicated Successfully!');
    }

    public function render()
    {
        $invoices = Invoice::query()
            ->when($this->searchCustomer, fn($q) => $q->where('customer', 'like', "%{$this->searchCustomer}%"))
            ->when($this->searchPart, fn($q) => $q->where('part_no', 'like', "%{$this->searchPart}%"))
            ->orderByDesc('invoice_id')
            ->paginate($this->perPage);

        return view('livewire.invoice.manage', [
            'invoices' => $invoices,
        ])->layout('layouts.app', ['title' => 'Invoice']);
    }
    public function searchByPartNo()
    {
        $this->searchPart = $this->partSearchInput;
        $this->resetPage();
    }

    public function searchByCustomer()
    {
        $this->searchCustomer = $this->customerSearchInput;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'searchPart', 'searchCustomer',
            'partSearchInput', 'customerSearchInput',
        ]);
        $this->resetPage();
    }

}