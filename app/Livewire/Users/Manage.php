<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;

class Manage extends Component
{
    public $confirmingDelete = false;
    public $userIdBeingDeleted;

    // For editing user
    public $editingUser = false;
    public $userIdBeingEdited;
    public $username;
    public $password;
    public $password_confirmation;

    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->userIdBeingDeleted = $id;
    }

    public function deleteUser()
    {
        User::findOrFail($this->userIdBeingDeleted)->delete();
        $this->confirmingDelete = false;
        session()->flash('success', 'User deleted successfully.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->userIdBeingEdited = $id;
        $this->username = $user->username;
        $this->editingUser = true;
    }

    public function updateUser()
{
    $rules = [
        'username' => 'required|string|max:255|unique:users,username,' . $this->userIdBeingEdited,
    ];

    // Only validate password if it was filled (not empty)
    if (!empty($this->password)) {
        $rules['password'] = 'required|string|min:6|confirmed';
    }

    $this->validate($rules);

    $user = User::findOrFail($this->userIdBeingEdited);
    $user->username = $this->username;

    if (!empty($this->password)) {
        $user->password = bcrypt($this->password);
    }

    $user->save();

    $this->editingUser = false;

    session()->flash('success', 'User updated successfully.');
}

    public function render()
    {
        $users = User::latest()->get();
        return view('livewire.users.manage', compact('users'))
            ->layout('layouts.app', ['title' => 'Manage Users']);
    }
}
