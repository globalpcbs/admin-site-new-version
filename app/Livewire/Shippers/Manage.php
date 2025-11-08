<?php
namespace App\Livewire\Shippers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\shipper_tb as Shipper;

class Manage extends Component
{
    use WithPagination;

    public $search = '';

     // SIMPLE alert properties
    public $alertMessage = '';
    public $alertType = '';
    protected $listeners = ['alert-hidden' => 'clearAlert'];

    public function clearAlert()
    {
        $this->alertMessage = '';
        $this->alertType = '';
    }

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
       $allShippers = Shipper::select('c_name')->distinct()->orderBy('c_name')->get();

        $shippers = Shipper::when($this->search, function ($query) {
            $query->where('c_name', $this->search);
        })->orderby('data_id','desc')->paginate(20); // 20 per page

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

    public function deleteShipper($id)
    {
        $this->deleteShipperId = $id;
        Shipper::where('data_id',$this->deleteShipperId)->delete();
        // SIMPLE: Just set the alert
        $this->alertMessage = 'Shipper deleted successfully.';
        $this->alertType = 'danger';
        
        // Clear alert after a short delay by forcing a re-render
        $this->dispatch('refresh-component');
    }

}
