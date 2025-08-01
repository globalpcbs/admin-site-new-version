<?php

namespace App\Livewire\PurchaseOrder;

use Livewire\Component;

class Duplicateasremark extends Component
{
    public function render()
    {
        return view('livewire.purchase-order.duplicateasremark')->layout('layouts.app', ['title' => 'PurchaseOrder']);
    }
}
