<?php

namespace App\Livewire\Company\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Rules\ComplexPassword;
use Livewire\Attributes\Rule;
use App\Mail\PasswordResetSuccessfulMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ResetPassword extends Component
{
    public $token;
    public $email;

    #[Rule(['required', 'confirmed', new ComplexPassword])]
    public $password = '';

    #[Rule('required')]
    public $password_confirmation = '';

    public function mount($token)
    {
        $this->token = $token;
        $this->email = request()->query('email');
    }

    public function resetPassword()
    {
        $this->validate();

        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $this->token)
            ->where('email', $this->email)
            ->first();

        if (!$tokenData) {
            flash()->error('Invalid token or email.');
            return;
        }

        $user = User::where('email', $this->email)
                    ->whereIn('user_type', ['CompanyRepresentative', 'CompanyAdmin'])
                    ->first();

        if (!$user) {
            flash()->error('Company representative or admin account not found.');
            return;
        }

        $user->password = Hash::make($this->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        event(new PasswordReset($user));

        // Send password reset successful email
        Mail::to($user->email)->send(new PasswordResetSuccessfulMail());

        flash()->success('Your password has been reset successfully.');
        return redirect()->route('company.login');
    }

    public function render()
    {
        return view('livewire.company.auth.reset-password');
    }
}
