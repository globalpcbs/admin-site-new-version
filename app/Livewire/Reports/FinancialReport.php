<?php

namespace App\Livewire\Reports;

use Livewire\Component;

class FinancialReport extends Component
{
    public function render()
    {
        return view('livewire.reports.financial-report')->layout('layouts.app', ['title' => 'Reports']);
    }
}
