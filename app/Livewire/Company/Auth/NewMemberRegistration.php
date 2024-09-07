<?php

namespace App\Livewire\Company\Auth;

use Ichtrojan\Otp\Otp;
use Livewire\Component;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewMemberRegistration extends Component
{
    public $email = '';
    public $confirmEmail = '';

    protected $rules = [
        'email' => 'required|email:rfc,dns|unique:users',
        'confirmEmail' => 'required|same:email',
    ];

    // Add this method for real-time validation
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function sendVerificationEmail()
    {
        $this->validate();

        try {
            $verificationToken = (new Otp())->generate($this->email, 'alpha_numeric', 30, 5)->token;

            $verificationUrl = route('company.email.verify', ['token' => $verificationToken, 'email' => $this->email]);

            Mail::to($this->email)->send(new VerifyEmail($verificationUrl));

            flash()->success('Verification email sent successfully!');

            $this->reset(['email', 'confirmEmail']);
            $this->dispatch('form-reset');
        } catch (\Exception $e) {
            flash()->error('An error occurred while sending the verification email. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.company.auth.new-member-registration');
    }
}
