<?php

namespace App\Livewire\Purchase;

use Livewire\Component;

class Add extends Component
{
    public function render()
    {
        return view('livewire.purchase.add')->layout('layouts.app', ['title' => 'Purchase']);
    }
}
