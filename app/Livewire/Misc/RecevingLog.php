<?php

namespace App\Livewire\Misc;

use Livewire\Component;
use App\Models\packing_tb_loged;
use App\Models\data_tb;
use App\Models\vendor_tb;
use Illuminate\Support\Facades\DB;

class RecevingLog extends Component
{
    public $records = [];

    public function mount()
    {
        $this->records = packing_tb_loged::orderByDesc('id')->get();
    }
    public function delete($id){
        packing_tb_loged::find($id)->delete();
        session()->flash('warning', 'Packing slip deleted successfully.');

    }
    public function duplicate($id)
    {
        DB::transaction(function () use ($id) {
            $original = packing_tb_loged::findOrFail($id);
            $copy = $original->replicate();
            $copy->save();
        });

        session()->flash('success', 'Packing slip duplicated successfully.');
       // return redirect()->route('misc.receving-log'); // if route is defined
    }

    public function render()
    {
        return view('livewire.misc.receving-log')->layout('layouts.app', ['title' => 'Misc']);
    }
}