<?php

namespace App\Livewire\Misc;

use Livewire\Component;

class DownloadMaterialCert extends Component
{
    public function render()
    {
        return view('livewire.misc.download-material-cert')->layout('layouts.app', ['title' => 'Misc']);
    }
}
