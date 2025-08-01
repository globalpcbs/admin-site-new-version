<?php

namespace App\Livewire\Misc;

use Livewire\Component;

class Labels extends Component
{
    public function render()
    {
        return view('livewire.misc.labels')->layout('layouts.app', ['title' => 'Misc']);
    }
}
