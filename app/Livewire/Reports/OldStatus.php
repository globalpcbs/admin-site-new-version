<?php

namespace App\Livewire\Reports;

use Livewire\Component;

class OldStatus extends Component
{
    public function render()
    {
        return view('livewire.reports.old-status')->layout('layouts.app', ['title' => 'Reports']);
    }
}
