<?php

namespace App\Http\Controllers\Affiliator\Auth;

use App\Http\Controllers\Controller;
use App\Models\Affiliator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AffiliateAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('affiliate.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('affiliator')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('affiliate.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('affiliate.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:affiliators',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
        ]);

        $affiliator = Affiliator::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'code' => strtoupper(Str::random(8)), // Auto-generate code on registration
            'discount_rate' => 20.00,
            'commission_rate' => 20.00,
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name' => $request->bank_account_name,
            'is_active' => true,
        ]);

        Auth::guard('affiliator')->login($affiliator);

        return redirect(route('affiliate.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('affiliator')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/affiliate/login');
    }
}
