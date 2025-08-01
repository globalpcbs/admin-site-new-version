<?php

namespace App\Livewire\Misc;

use Livewire\Component;

class PackingSlipsReport extends Component
{
    public function render()
    {
        return view('livewire.misc.packing-slips-report')->layout('layouts.app', ['title' => 'Misc']);
    }
}
