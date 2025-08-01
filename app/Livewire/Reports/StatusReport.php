<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\porder_tb;

class StatusReport extends Component
{
    public $orders = [];
    public $showModal = false;
    public $orderId;
    public $cus_due;
    public $sup_due;
    public $selectedPoid;
    
    public $showNoteModal = false;
    public $note = '';
    public $poidForNote;
    public $from, $to, $partNumber, $customerName, $vendorName;

    public function openNoteModal($poid)
    {
        $this->poidForNote = $poid;
        $order = porder_tb::find($poid);
        $this->note = $order?->note ?? '';
        $this->showNoteModal = true;
    }

    public function saveNote()
    {
        $order = porder_tb::find($this->poidForNote);
        if ($order) {
            $order->note = $this->note;
            $order->save();
        }
        $this->showNoteModal = false;
        $this->refreshData();
        session()->flash('success', 'Note updated successfully.');
    }
    // Called directly by wire:click
    public function openModal($id)
    {
        $order = porder_tb::findOrFail($id);
        $this->selectedPoid = $order->poid;
        $this->orderId = $order->poid;
        $this->cus_due = $order->cus_due;
        $this->sup_due = $order->supli_due;
        $this->showModal = true;
    }

    public function updateDueDates()
    {
        $order = porder_tb::findOrFail($this->orderId);
        $order->cus_due = $this->cus_due;
        $order->supli_due = $this->sup_due;
        $order->save();

        $this->reset(['showModal', 'orderId', 'cus_due', 'sup_due']);
        $this->refreshData();
        session()->flash('success', 'Due dates updated successfully!');
    }
    public function closeModal()
    {
        $this->showModal = false;
    }
    public function mount()
    {
        $this->refreshData();
    }
    public function updated($propertyName)
{
    $this->refreshData();
}

public function refreshData()
{
    $wheres = [];

    if ($this->from && $this->to) {
        $wheres[] = "STR_TO_DATE(p.dweek, '%m-%d-%Y') BETWEEN STR_TO_DATE('{$this->from}', '%Y-%m-%d') AND STR_TO_DATE('{$this->to}', '%Y-%m-%d')";
    }

    if ($this->partNumber) {
        $wheres[] = "p.part_no LIKE '%{$this->partNumber}%'";
    }

    if ($this->customerName) {
        $wheres[] = "p.customer LIKE '%{$this->customerName}%'";
    }

    if ($this->vendorName) {
        $wheres[] = "v.c_shortname LIKE '%{$this->vendorName}%'";
    }

    $wherestr = count($wheres) ? 'WHERE ' . implode(' AND ', $wheres) : '';
    $ord_by = 'ORDER BY p.poid ASC';

    $query = "
        SELECT p.*, i.invoice_id, i.podate invoicedon, v.c_shortname vc,
               UNIX_TIMESTAMP(STR_TO_DATE(p.dweek,'%m-%d-%Y')) dw
        FROM porder_tb p
        LEFT OUTER JOIN invoice_tb i ON (p.part_no = i.part_no AND p.rev = i.rev AND p.po = i.po)
        LEFT OUTER JOIN vendor_tb v ON v.data_id = p.vid
        $wherestr
        $ord_by
        LIMIT 200
    ";

    $this->orders = DB::select($query);
}
public function resetFilters()
{
    $this->from = null;
    $this->to = null; 
    $this->partNumber = ''; 
    $this->customerName = '';
    $this->vendorName = '';

    // optionally, reset pagination too
    $this->refreshData();
}
    public function toggleOrder($poid, $isChecked)
    {
        // You may update your database or perform logic based on $poid and $isChecked

        // Example: Update the "allow" column to true/false
        DB::table('porder_tb')
            ->where('poid', $poid)
            ->update(['allow' => $isChecked ? 'true' : 'false']);

        // Optionally refresh the $orders data
        //$this->mount();
        session()->flash('success', 'WT Status Has Been updated successfully!');
    }
    public function render()
    {
            // dd($this->orders);
        return view('livewire.reports.status-report')
           ->layout('layouts.app', ['title' => 'Reports']);
    }
}