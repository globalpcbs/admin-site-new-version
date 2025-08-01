<?php

namespace App\Livewire\Qoutes\Project;

use Livewire\Component;

class Add extends Component
{
    public function render()
    {
        return view('livewire.qoutes.project.add')->layout('layouts.app', ['title' => 'Add Project']);
    }
}
