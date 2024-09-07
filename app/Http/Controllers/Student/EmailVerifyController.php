<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmailVerifyController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $token, Otp $otp)
    {
        $email = $request->input('email');

        $isValidated = $otp->validate($email, $token);

        if (!$isValidated->status) {
            flash()->error("Invalid verification token!");

            return redirect()->route('student.new-member-registration');
        }

        $newValidationToken = $otp->generate($email, 'alpha_numeric', 10, 5)->token;

        $username = explode('@', $email)[0] . '_' . Str::random(5);


        return redirect()->route('student.user-registration')
            ->with('username', $username)
            ->with('email', $email)
            ->with('token', $newValidationToken);
    }
}
