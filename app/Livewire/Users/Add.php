<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Add extends Component
{
    public $username;
    public $password;

    protected $rules = [
        'username' => 'required|string|unique:users,username|min:3',
        'password' => 'required|string|min:6',
    ];

    public function addUser()
    {
        $this->validate();

        $user = new User();
        $user->username = $this->username;
        $user->password = Hash::make($this->password);
        $user->save();

        session()->flash('success', 'User added successfully.');

        $this->reset(['username', 'password']);
        return redirect(route('users.manage'));
    }

    public function render()
    {
        return view('livewire.users.add')->layout('layouts.app', ['title' => 'Add New User']);
    }
}