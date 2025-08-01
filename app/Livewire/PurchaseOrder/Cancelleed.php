<?php

namespace App\Livewire\PurchaseOrder;

use Livewire\Component;

class Cancelleed extends Component
{
    public function render()
    {
        return view('livewire.purchase-order.cancelleed')->layout('layouts.app', ['title' => 'PurchaseOrder']);
    }
}
