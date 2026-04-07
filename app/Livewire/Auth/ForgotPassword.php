<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class ForgotPassword extends Component
{
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ];
    }

    public function resetPassword()
    {
        $validated = $this->validate();

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            $this->addError('email', 'Email tidak ditemukan');

            return;
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ])->save();

        return redirect()
            ->route('login')
            ->with('status', 'Password berhasil direset. Silakan login.');
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
