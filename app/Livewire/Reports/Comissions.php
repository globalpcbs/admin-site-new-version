<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\rep_tb;
use App\Models\invoice_tb;

class Comissions extends Component
{
    use WithPagination;

    public $selectedRep = '';
    public $search = '';

    protected $queryString = ['selectedRep', 'search'];

    public function updatingSelectedRep()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $reps = rep_tb::orderBy('r_name')->get();

        $invoices = collect(); // default empty if no rep selected

        if ($this->selectedRep) {
            $rep = rep_tb::find($this->selectedRep);

            $query = invoice_tb::where('salesrep', 'LIKE', "%{$rep->r_name}%");

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('customer', 'like', "%{$this->search}%")
                      ->orWhere('part_no', 'like', "%{$this->search}%")
                      ->orWhere('invoice_id', 'like', "%{$this->search}%");
                });
            }

            $invoices = $query->orderByDesc('podate')->paginate(15);
        }

        return view('livewire.reports.comissions', [
            'reps' => $reps,
            'invoices' => $invoices,
        ])->layout('layouts.app', ['title' => 'Comission Reports']);
    }
}