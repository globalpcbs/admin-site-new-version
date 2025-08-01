<?php

namespace App\Livewire\Misc;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\notes_tb;

class ManageNotes extends Component
{
    use WithPagination;

    public $perPage = 50;

    public function render()
    {
        return view('livewire.misc.manage-notes', [
            'notes' => notes_tb::orderBy('nid')->paginate($this->perPage),
        ])->layout('layouts.app', ['title' => 'Manage Generic Notes']);
    }
}