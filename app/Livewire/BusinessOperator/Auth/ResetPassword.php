<?php

namespace App\Livewire\BusinessOperator\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Rules\ComplexPassword;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetSuccessfulMail;
use Illuminate\Support\Facades\Auth;
class ResetPassword extends Component
{
    #[Rule('required')]
    public $token;

    #[Rule('required|email')]
    public $email;

    #[Rule(['required', 'min:8', 'confirmed', new ComplexPassword])]
    public $password = '';

    public $password_confirmation = '';

    public function mount($token)
    {
        $this->token = $token;
        $this->email = request()->query('email');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function resetPassword()
    {
        $this->validate();

        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $this->token)
            ->where('email', $this->email)
            ->first();

        if (!$tokenData) {
            $this->addError('email', 'Invalid token or email.');
            return;
        }

        $user = User::where('email', $this->email)
                    ->where('user_type', 'BusinessOperator')
                    ->first();

        if (!$user) {
            $this->addError('email', 'User not found.');
            return;
        }

        $user->password = bcrypt($this->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        event(new PasswordReset($user));

        // Send password reset successful email
        Mail::to($user->email)->send(new PasswordResetSuccessfulMail($user));

        flash()->success('Your password has been reset successfully.');
        if (Auth::check()) {
            Auth::logout();
        }

        return redirect()->route('business-operator.login');
    }

    public function render()
    {
        return view('livewire.business-operator.auth.reset-password');
    }
}
