<?php

namespace App\Livewire\Vendors\Eng;

use Livewire\Component;
use App\Models\vendor_enggcont_tb;
use App\Models\Customer;
use Livewire\WithPagination;

class ManageContact extends Component
{
    use WithPagination;

    public $search = '';
    public $deleteId;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getFilteredContacts()
    {
        return vendor_enggcont_tb::whereHas('vendor', function ($query) {
            $query->where('c_name', 'like', "%{$this->search}%");
        })
        ->with('vendor')
        ->latest()
        ->paginate(10);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function delete()
    {
        vendor_enggcont_tb::findOrFail($this->deleteId)->delete();
        session()->flash('success', 'Contact deleted successfully.');
        $this->deleteId = null;
    }

    public function render()
    {
        $contacts = $this->getFilteredContacts();
        return view('livewire.vendors.eng.manage-contact', compact('contacts'))
            ->layout('layouts.app', ['title' => 'Manage Vendors Eng Contact']);
    }
}
