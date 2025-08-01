<?php

namespace App\Livewire\Misc;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\file_upload as FileUpload; // This is your file_upload table's model
use Carbon\Carbon;

class Pofileuploadswork extends Component
{
    use WithFileUploads;

    public $customer, $part_no, $rev;
    public $file, $date;
    public $uploadedFiles = [];

    public function mount($customer, $part_no, $rev)
    {
        $this->customer = $customer;
        $this->part_no = $part_no;
        $this->rev = $rev;
        $this->loadFiles();
    }

    public function loadFiles()
    {
        $this->uploadedFiles = FileUpload::where('customer', $this->customer)
            ->where('part_no', $this->part_no)
            ->where('rev', $this->rev)
            ->orderByDesc('id')
            ->get();
    }

    public function uploadfiler()
    {
       // dd("worker");
        $this->validate([
            'file' => 'required|file|max:10240', // Max 10MB
            'date' => 'required|date',
        ]);

        $filename = time() . '_' . Str::slug($this->file->getClientOriginalName()).".".$this->file->getClientOriginalExtension();
        $path = $this->file->storeAs('uploads/po_files', $filename, 'public');

        FileUpload::create([
            'customer' => $this->customer,
            'part_no' => $this->part_no,
            'rev' => $this->rev,
            'name' => $filename,
            'path' => 'storage/' . $path,
            'date' => $this->date,
        ]);

        $this->reset('file', 'date');
        $this->loadFiles();

        session()->flash('success', 'File uploaded successfully.');
    }
    public function testUpload(){
        dd("workr");
    }
    public function deleteFile($id)
    {
        $file = FileUpload::findOrFail($id);
        if (Storage::disk('public')->exists(Str::after($file->path, 'storage/'))) {
            Storage::disk('public')->delete(Str::after($file->path, 'storage/'));
        }
        $file->delete();
        $this->loadFiles();

        session()->flash('success', 'File deleted successfully.');
    }

    public function render()
    {
        return view('livewire.misc.pofileuploadswork')->layout('layouts.app', ['title' => 'PO File Upload Details']);
    }
}