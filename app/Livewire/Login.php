<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $username;
    public $password;

    public function login()
    {
        $this->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to login by username and password
        if (Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            session()->flash('success', 'Welcome back, ' . ucfirst(Auth::user()->username) . ' !');
            return redirect()->route('dashboard');
        } else {
            session()->flash('error', 'Invalid username or password.');
            return redirect()->route('login');
        }
    }

    public function render()
    {
        return view('livewire.login')->layout('layouts.app');
    }
}