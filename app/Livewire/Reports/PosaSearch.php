<?php

namespace App\Livewire\Reports;

use Livewire\Component;

class PosaSearch extends Component
{
    public function render()
    {
        return view('livewire.reports.posa-search')->layout('layouts.app', ['title' => 'Reports']);
    }
}
