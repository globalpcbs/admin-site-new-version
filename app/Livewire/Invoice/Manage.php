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
        // for search ...
    public $searchPartNoInput = '';
    public $matches    = [];          // array of suggestions ⬅️  NEW
    public $matches_partno = []; // array of part no ..
    public $searchCustomerInput = '';
     // SIMPLE alert properties
    public $alertMessage = '';
    public $alertType = '';
    protected $listeners = ['alert-hidden' => 'clearAlert'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

    
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
         // SIMPLE: Just set the alert
        $this->alertMessage = 'Payment Details Updated Successfully!';
        $this->alertType = 'success';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
    }

    public function togglePaid($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->ispaid = $invoice->ispaid == 1 ? 0 : 1;
        $invoice->save();
        
    }
    public function togglePending($id){
      //  dd($id);
        $invoice = Invoice::findOrFail($id);
        $invoice->pending = $invoice->pending == 1 ? 0 : 1;
        $invoice->save();
       // session()->flash('success', 'Past Due Updated Successfully!');
         // SIMPLE: Just set the alert
        $this->alertMessage = 'Past Due Updated Successfully!';
        $this->alertType = 'success';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
    }
    public function toggleMailStop($id){
        $invoice = Invoice::findOrFail($id);
        $invoice->mailstop = $invoice->mailstop == 1 ? 0 : 1;
        $invoice->save();
        //session()->flash('success', 'Mail Stop Updated Successfully!');
         // SIMPLE: Just set the alert
        $this->alertMessage = 'Mail Stop Updated Successfully!';
        $this->alertType = 'success';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');

    }
    public function delete($id)
    {
        Invoice::findOrFail($id)->delete();
        //session()->flash('warning', 'Invoice Deleted Successfully!');
         // SIMPLE: Just set the alert
        $this->alertMessage = 'Invoice Deleted Successfully!';
        $this->alertType = 'danger';
        
        // Clear alert after a short delay by forcing a re-render
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
         // SIMPLE: Just set the alert
        $this->alertMessage = 'Quote duplicated successfully.';
        $this->alertType = 'success';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
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
       // search ...
    public function searchq(){
         // assign the input values to the actual search vars
        $this->searchPart = $this->searchPartNoInput;
       // dd($this->partSearchInput);
        //  dd($this->searchPartNo);
            // reset pagination
        $this->resetPage();

        // clear the input fields (but keep actual filters intact)
       $this->reset(['searchPartNoInput']);  
    }
    public function searchbyCustomer() {
       // $customer = data_tb::where('c_name',$this->searchCustomerInput)->first();
       // dd($customer->data_id);
        $this->searchCustomer = $this->searchCustomerInput;
       // reset pagination
       $this->resetPage();

        // clear the input fields (but keep actual filters intact)
       $this->reset(['searchCustomerInput']);    
    }
        // search ...
    public function onKeyUp(string $value){
       // dd($value);
         if (mb_strlen(trim($value)) < 2) {
            $this->matches = [];
            return;
        }
        $this->matches = Invoice::query()
        ->where('customer', 'like', "%{$value}%")
        ->select('customer')
        ->distinct()
        ->get()
        ->toArray();
        //dd($this->matches);
    }   
    public function useMatch($i){
       // dd($this->matches[$i]['data_id']);
        $this->searchCustomerInput = $this->matches[$i]['customer'];
        $this->matches = [];
    }
    public function usekeyupno(string $value){
         if (mb_strlen(trim($value)) < 2) {
            $this->matches_partno = [];
            return;
        }
        $this->matches_partno = Invoice::query()
        ->select('part_no')
        ->where('part_no', 'like', "%{$value}%")
        ->distinct()
        ->get()
        ->toArray();
    }
    public function useMatchpn($i){
        $this->searchPartNoInput = $this->matches_partno[$i]['part_no'];
        $this->matches_partno = [];
    }
        public function resetFilters()
    {
        $this->reset(['searchPart', 'searchCustomer']);
    }

}