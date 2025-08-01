<?php

namespace App\Livewire\Misc;

use Livewire\Component;

class OrderPlacedReport extends Component
{
    public function render()
    {
        return view('livewire.misc.order-placed-report')->layout('layouts.app', ['title' => 'Misc']);
    }
}
