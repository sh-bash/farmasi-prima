<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class Login extends Component
{
    public $email;
    public $password;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required'
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt([
            'email' => $this->email,
            'password' => $this->password
        ])) {

            $user = Auth::user();

            if ($user->hasRole('patient') && ! $user->patient()->exists()) {
                Auth::logout();
                $this->addError('email', 'Data pasien tidak ditemukan, silahkan buat user melalui master pasien');
                return;
            }

            session()->regenerate();
            return redirect()->route('dashboard');
        }

        $this->addError('email', 'Email atau password salah');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
