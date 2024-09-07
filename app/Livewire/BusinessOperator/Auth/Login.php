<?php

namespace App\Livewire\BusinessOperator\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    public $errorMessage = '';
    public $isBlocked = false;
    public $blockDuration = 0;
    public $showPassword = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        $key = 'login.' . Str::lower($this->email);
        $maxAttempts = 3;
        $decayMinutes = 2;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $this->isBlocked = true;
            $this->blockDuration = RateLimiter::availableIn($key);
            $this->errorMessage = "Too many login attempts. Please try again in {$this->blockDuration} seconds or reset your password.";
            return;
        }

        $user = User::where('email', $this->email)->first();

        if (!$user || $user->user_type !== 'BusinessOperator') {
            RateLimiter::hit($key, $decayMinutes * 60);
            $remainingAttempts = RateLimiter::remaining($key, $maxAttempts);
            $this->errorMessage = "Invalid email or password. {$remainingAttempts} attempts remaining.";
            return;
        }

        if ($user->login_permission_category === 'Pending') {
            $this->errorMessage = 'Your account is pending approval. Please wait for admin confirmation.';
            return;
        }

        if ($user->login_permission_category === 'NotAllowed') {
            $this->errorMessage = 'Your account has been rejected. Please contact the administrator.';
            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($key);
            return redirect()->intended(route('home'));
        }

        RateLimiter::hit($key, $decayMinutes * 60);
        $remainingAttempts = RateLimiter::remaining($key, $maxAttempts);
        $this->errorMessage = "Invalid email or password. {$remainingAttempts} attempts remaining.";
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.business-operator.auth.login');
    }
}
