<?php
namespace App\Livewire\Shippers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\shipper_tb as Shipper;

class Manage extends Component
{
    use WithPagination;

    public $search = '';

    // Reset to page 1 when search changes
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function filterShippers($value)
{
    $this->search = $value;
    $this->resetPage();
}

    public function render()
    {
        $allShippers = Shipper::select('c_name')->distinct()->get();

        $shippers = Shipper::when($this->search, function ($query) {
            $query->where('c_name', $this->search);
        })->paginate(20); // 20 per page

        return view('livewire.shippers.manage', [
            'shippers' => $shippers,
            'allShippers' => $allShippers
        ])->layout('layouts.app', ['title' => 'Manage Shippers']);
    }
    public function mount(){
        
    }
    public $confirmingDelete = false;
    public $deleteShipperId = null;

    public function confirmDelete($id)
    {
        $this->deleteShipperId = $id;
        $this->confirmingDelete = true;
    }

    public function deleteShipper()
    {
        Shipper::where('data_id',$this->deleteShipperId)->delete();
        session()->flash('warning', 'Shipper deleted successfully.');
        $this->confirmingDelete = false;
    }

}
