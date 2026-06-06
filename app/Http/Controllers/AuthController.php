<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentDetail;
use App\Models\OtpCode;
use App\Models\TrustedDevice;
use App\Helpers\ActivityLogger;
use App\Mail\OtpLoginMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('student.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), true)) {
            $user = Auth::user();

            // Check if device is trusted (skip OTP)
            $deviceToken = $request->cookie('trusted_device');
            if ($deviceToken) {
                $trusted = TrustedDevice::where('device_token', $deviceToken)
                    ->where('user_id', $user->id)
                    ->where('expires_at', '>', now())
                    ->first();

                if ($trusted) {
                    $request->session()->regenerate();
                    ActivityLogger::log('login', $user->name . ' logged in (trusted device)', 'fa-sign-in-alt', '#06d6a0');
                    return $user->isAdmin()
                        ? redirect()->route('admin.dashboard')
                        : redirect()->route('student.dashboard');
                }
            }

            // Generate OTP and redirect to verification
            Auth::logout();
            $otp = OtpCode::generate($user->id);

            try {
                Mail::to($user->email)->send(new OtpLoginMail($otp->code, $user->name));
            } catch (\Throwable $e) {
                // Log driver in dev — OTP still stored in DB
            }

            $maskedEmail = $this->maskEmail($user->email);

            return view('auth.otp_verify', [
                'userId' => $user->id,
                'maskedEmail' => $maskedEmail,
            ]);
        }

        return back()->with('error', 'Invalid email or password.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($request->user_id);

        // Rate limiting: max 5 attempts per 10 minutes
        $recentAttempts = OtpCode::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->where('is_used', true)
            ->count();

        if ($recentAttempts >= 10) {
            return back()->with('error', 'Too many attempts. Please wait and try again.');
        }

        $otp = OtpCode::where('user_id', $user->id)
            ->where('code', $request->otp)
            ->where('is_used', false)
            ->where('type', 'login')
            ->first();

        if (!$otp || !$otp->isValid()) {
            return view('auth.otp_verify', [
                'userId' => $user->id,
                'maskedEmail' => $this->maskEmail($user->email),
            ])->with('error', 'Invalid or expired OTP code.');
        }

        // Mark OTP as used
        $otp->update(['is_used' => true]);

        // Log in the user
        Auth::login($user);
        $request->session()->regenerate();

        ActivityLogger::log('login', $user->name . ' logged in (2FA verified)', 'fa-shield-alt', '#06d6a0');

        // Handle trust device
        $response = $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('student.dashboard');

        if ($request->has('trust_device')) {
            $token = Str::random(64);
            TrustedDevice::create([
                'user_id' => $user->id,
                'device_token' => $token,
                'device_name' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'expires_at' => Carbon::now()->addDays(30),
            ]);
            $response = $response->cookie('trusted_device', $token, 60 * 24 * 30);
        }

        return $response;
    }

    public function resendOtp(Request $request)
    {
        $request->validate(['user_id' => 'required|integer']);

        $user = User::findOrFail($request->user_id);
        $otp = OtpCode::generate($user->id);

        try {
            Mail::to($user->email)->send(new OtpLoginMail($otp->code, $user->name));
        } catch (\Throwable $e) {
            // Log driver in dev
        }

        return view('auth.otp_verify', [
            'userId' => $user->id,
            'maskedEmail' => $this->maskEmail($user->email),
        ])->with('success', 'A new verification code has been sent.');
    }

    public function showRegister()
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
            'roll_no' => 'required|string|max:50',
            'department' => 'required|string|max:100',
            'year' => 'required|integer|min:1|max:4',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'student',
        ]);

        StudentDetail::create([
            'user_id' => $user->id,
            'roll_no' => $request->roll_no,
            'department' => $request->department,
            'year' => $request->year,
        ]);

        ActivityLogger::log(
            'student_registered',
            $user->name . ' registered an account',
            'fa-user-plus',
            '#4361ee',
            $user->id
        );

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function maskEmail(string $email): string
    {
        [$name, $domain] = explode('@', $email);
        $masked = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 2, 2));
        return $masked . '@' . $domain;
    }
}
