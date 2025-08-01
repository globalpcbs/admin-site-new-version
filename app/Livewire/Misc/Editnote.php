<?php

namespace App\Livewire\Misc;

use Livewire\Component;
use App\Models\notes_tb;

class Editnote extends Component
{
    public $ntype;
    public $ntitle;
    public $ntext;

    public function mount($ntype)
    {
        $note = notes_tb::where('ntype', $ntype)->firstOrFail();

        $this->ntype = $note->ntype;
        $this->ntitle = $note->ntitle;
        $this->ntext = $note->ntext;
    }

    public function update()
    {
        $this->validate([
            'ntitle' => 'required|string|max:255',
            'ntext' => 'required|string',
        ]);

        $note = notes_tb::where('ntype', $this->ntype)->firstOrFail();
        $note->ntitle = $this->ntitle;
        $note->ntext = $this->ntext;
        $note->save();

        session()->flash('success', 'Note updated successfully!');
        return redirect()->route('misc.manage-notes');
    }

    public function render()
    {
        return view('livewire.misc.editnote')->layout('layouts.app', ['title' => 'Edit Note']);
    }
}