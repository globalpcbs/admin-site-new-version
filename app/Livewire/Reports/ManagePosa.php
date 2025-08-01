<?php

namespace App\Livewire\Reports;

use Livewire\Component;

class ManagePosa extends Component
{
    public function render()
    {
        return view('livewire.reports.manage-posa')->layout('layouts.app', ['title' => 'Reports']);
    }
}
