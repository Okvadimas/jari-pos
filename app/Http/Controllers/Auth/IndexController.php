<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Auth\AuthService;

// Load Request
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

// Load Model
use App\Models\Campaign;

class IndexController extends Controller
{
    public function login()
    {
        $title   = 'Login | Jari POS';
        $sliders = Campaign::where('type', 'slider')->where('is_published', 1)->get();
        $css     = 'resources/css/pages/auth/login.css';
        $js      = 'resources/js/pages/auth/login.js';

        return view('auth.login', compact('css', 'js', 'title', 'sliders'));
    }

    public function processLogin(LoginRequest $request)
    {
        $result = AuthService::login(
            $request->only('username', 'password'),
            $request
        );

        return $result['status']
            ? $this->successResponse($result['message'])
            : $this->errorResponse($result['message'], 422);
    }

    public function register()
    {
        $title   = 'Register | Jari POS';
        $sliders = Campaign::where('type', 'slider')->where('is_published', 1)->get();
        $css     = 'resources/css/pages/auth/register.css';
        $js      = 'resources/js/pages/auth/register.js';

        return view('auth.register', compact('css', 'js', 'title', 'sliders'));
    }

    public function processRegister(RegisterRequest $request)
    {
        $result = AuthService::register($request->validated());

        return $result['status']
            ? $this->successResponse($result['message'])
            : $this->errorResponse($result['message'], 500);
    }

    /**
     * Show email verification notice page
     */
    public function verifyNotice()
    {
        $title = 'Verifikasi Email | Jari POS';
        $css   = 'resources/css/pages/auth/verify-email.css';
        $js    = 'resources/js/pages/auth/verify-email.js';

        return view('auth.verify-email', compact('title', 'css', 'js'));
    }

    /**
     * Handle email verification link
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $result = AuthService::verifyEmail($id, $hash);

        return redirect()->route('login')
            ->with($result['type'], $result['message']);
    }

    /**
     * Resend email verification
     */
    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email'], [
            'email.required' => 'Email wajib diisi',
            'email.email'    => 'Email tidak valid',
        ]);

        $result = AuthService::resendVerification($request->email);

        return $result['status']
            ? $this->successResponse($result['message'])
            : $this->errorResponse($result['message'], 404);
    }

    public function resetPassword()
    {
        return view('auth.reset-password');
    }

    public function processResetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $result = AuthService::resetPassword($request->email);

        if (!$result['status']) {
            return back()->withErrors(['email' => $result['message']]);
        }

        return redirect()->route('login');
    }

    public function logout()
    {
        \Illuminate\Support\Facades\Auth::logout();
        return redirect()->route('login');
    }

    public function lockScreen()
    {
        return view('auth.lock-screen');
    }

    public function unlockScreen(Request $request)
    {
        $request->validate(['password' => 'required']);

        if (\Illuminate\Support\Facades\Hash::check($request->password, \Illuminate\Support\Facades\Auth::user()->password)) {
            return $this->successResponse('Berhasil membuka kunci layar');
        }

        return $this->errorResponse('Kata sandi salah', 422);
    }
}
