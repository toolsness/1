<?php

namespace App\Livewire\Company\Auth;

use App\Models\User;
use App\Models\Company;
use App\Models\CompanyRepresentative;
use App\Rules\ComplexPassword;
use App\Rules\EnglishName;
use App\Rules\JapaneseKanjiName;
use App\Rules\JapaneseKatakanaName;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountRegistrationSuccessfulCompany;

class UserRegistration extends Component
{
    public $token;
    public $username;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $companyId;
    public $nameKanji;
    public $nameKatakana;
    public $contactPhoneNumber;
    public $agreeTerms = false;

    protected $validationAttributes = [
        'password_confirmation' => 'password confirmation',
    ];

    public function rules()
    {
        return [
            'username' => 'required|unique:users,username',
            'name' => ['required', 'string', 'max:35', new EnglishName],
            'email' => 'required|email|max:70|unique:users,email',
            'password' => ['required', 'min:8', new ComplexPassword],
            'password_confirmation' => 'required|same:password',
            'companyId' => 'required|exists:companies,id',
            'nameKanji' => ['required', 'string', 'max:255', new JapaneseKanjiName],
            'nameKatakana' => ['required', 'string', 'max:255', new JapaneseKatakanaName],
            'contactPhoneNumber' => 'required|string|max:255',
            'agreeTerms' => 'accepted',
        ];
    }

    public function mount()
    {
        $this->username = session()->get('username');
        $this->email = session()->get('email');
        $this->token = session()->get('token');

        if (!$this->username || !$this->email || !$this->token) {
            return redirect()->route('company.new-member-registration');
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function checkErrorsAndSubmit()
    {
        $this->validate();

        if ($this->getErrorBag()->isEmpty()) {
            $this->register();
        } else {
            flash()->error('Please correct the errors before submitting.');
        }
    }

    public function register()
    {
        if (!(new Otp())->validate($this->email, $this->token)->status) {
            flash()->error('Invalid verification token!');
            return redirect()->route('company.new-member-registration');
        }

        DB::transaction(function () {
            $user = User::create([
                'username' => $this->username,
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'user_type' => 'CompanyRepresentative',
                'login_permission_category' => 'Pending', // Set to Pending by default
                'email_verified_at' => now(),
                'remember_token' => Str::random(60),
            ]);

            CompanyRepresentative::create([
                'user_id' => $user->id,
                'company_id' => $this->companyId,
                'name_kanji' => $this->nameKanji,
                'name_katakana' => $this->nameKatakana,
                'contact_phone_number' => $this->contactPhoneNumber,
            ]);
        });

        flash()->success('Your account has been created successfully! Please wait for admin approval.');
        Mail::to($this->email)->send(new AccountRegistrationSuccessfulCompany());
        return redirect()->route('company.login');
    }

    public function getLabel($field)
    {
        $labels = [
            'username' => 'User ID',
            'companyId' => 'Company Name',
            'name' => 'Name (English)',
            'nameKanji' => 'Name (Kanji)',
            'nameKatakana' => 'Name (Katakana)',
            'email' => 'E-mail Address',
            'contactPhoneNumber' => 'Contact Phone Number',
            'password' => 'Password',
            'password_confirmation' => 'Password (for confirmation)',
        ];

        return $labels[$field] ?? ucfirst($field);
    }

    public function getPlaceholder($field)
    {
        $placeholders = [
            'name' => 'Enter your name in English',
            'nameKanji' => '漢字で名前を入力してください',
            'nameKatakana' => 'カタカナで名前を入力してください',
        ];

        return $placeholders[$field] ?? '';
    }

    public function render()
    {
        $companies = Company::orderBy('name')->get();
        return view('livewire.company.auth.user-registration', compact('companies'));
    }
}
