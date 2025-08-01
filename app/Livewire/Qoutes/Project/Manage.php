<?php

namespace App\Livewire\Qoutes\Project;

use Livewire\Component;

class Manage extends Component
{
    public function render()
    {
        return view('livewire.qoutes.project.manage')->layout('layouts.app', ['title' => 'Manage Project']);
    }
}
