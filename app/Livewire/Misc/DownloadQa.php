<?php

namespace App\Livewire\Misc;

use Livewire\Component;

class DownloadQa extends Component
{
    public function render()
    {
        return view('livewire.misc.download-qa')->layout('layouts.app', ['title' => 'Misc']);
    }
}
