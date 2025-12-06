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
    
    // Alpine.js compatible filter properties
    public $searchPartNoInput = '';
    public $searchCustomerInput = '';
    public $searchPartNo = '';
    public $searchCustomer = '';

    public $showPaymentModal = false;
    public $selectedInvoiceId;
    public $paytype = '';
    public $paydetail = '';
    public $paydate = '';
    public $paynote = '';

    // SIMPLE alert properties
    public $alertMessage = '';
    public $alertType = '';
    protected $listeners = ['alert-hidden' => 'clearAlert'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    public function updatingSearchPartNo() { 
        $this->resetPage(); 
    }
    
    public function updatingSearchCustomer() { 
        $this->resetPage(); 
    }

    // Alpine.js compatible search methods
    public function searchq()
    {
        $this->searchPartNo = $this->searchPartNoInput;
        $this->resetPage();
    }

    public function searchbyCustomer()
    {
        $this->searchCustomer = $this->searchCustomerInput;
        $this->resetPage();
    }

    public function filterclose()
    {
        $this->reset([
            'searchPartNoInput',
            'searchCustomerInput',
            'searchPartNo',
            'searchCustomer'
        ]);
        $this->resetPage();
    }

    public function openPaymentModal($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->selectedInvoiceId = $invoice->invoice_id;
        $invoice->ispaid = 1;
        $invoice->save();
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
        
        $this->alertMessage = 'Payment Details Updated Successfully!';
        $this->alertType = 'success';
        
        $this->dispatch('refresh-component');
    }

    public function togglePaid($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->ispaid = $invoice->ispaid == 1 ? 0 : 1;
        $invoice->save();
    }
    
    public function togglePending($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->pending = $invoice->pending == 1 ? 0 : 1;
        $invoice->save();
        
        $this->alertMessage = 'Past Due Updated Successfully!';
        $this->alertType = 'success';
        
        $this->dispatch('refresh-component');
    }
    
    public function toggleMailStop($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->mailstop = $invoice->mailstop == 1 ? 0 : 1;
        $invoice->save();
        
        $this->alertMessage = 'Mail Stop Updated Successfully!';
        $this->alertType = 'success';
        
        $this->dispatch('refresh-component');
    }
    
    public function delete($id)
    {
        Invoice::findOrFail($id)->delete();
        
        $this->alertMessage = 'Invoice Deleted Successfully!';
        $this->alertType = 'danger';
        
        $this->dispatch('refresh-component');
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
        
        $this->alertMessage = 'Invoice duplicated successfully.';
        $this->alertType = 'success';
        
        $this->dispatch('refresh-component');
    }

    public function render()
    {
        $invoices = Invoice::query()
            ->when($this->searchCustomer, fn($q) => $q->where('customer', 'like', "%{$this->searchCustomer}%"))
            ->when($this->searchPartNo, fn($q) => $q->where('part_no', 'like', "%{$this->searchPartNo}%"))
            ->orderByDesc('invoice_id')
            ->paginate($this->perPage);

        return view('livewire.invoice.manage', [
            'invoices' => $invoices,
        ])->layout('layouts.app', ['title' => 'Invoice']);
    }
}