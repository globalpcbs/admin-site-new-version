<?php

namespace App\Livewire\Reports;

use Livewire\Component;

class PastDueInvoice extends Component
{
    public function render()
    {
        return view('livewire.reports.past-due-invoice')->layout('layouts.app', ['title' => 'Reports']);
    }
}
