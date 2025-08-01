<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePassword extends Component
{
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function changePassword()
    {
        $this->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('Current password is incorrect.');
                }
            }],
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        // Update password
        $user = Auth::user();
        $user->password = Hash::make($this->new_password);
        $user->save();

        // Reset fields
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        session()->flash('success', 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.users.change-password')->layout('layouts.app', ['title' => 'Change Password']);
    }
}
