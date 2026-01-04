<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Mail\ResetPasswordMail;

use App\Models\User;
use App\Models\Campaign;

class IndexController extends Controller
{
    public function login() {
        $title = 'Login | Jari POS';
        $sliders = Campaign::where('type', 'slider')->where('status', 'active')->get();      
        $js = 'resource/js/pages/auth/login.js';  
        return view('auth.login', compact('title', 'sliders', 'js'));
    }

    public function processLogin(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username' => 'Username wajib diisi',
            'password' => 'Kata Sandi wajib diisi',
        ]);
   
        if($validator->stopOnFirstFailure()->fails()){
            return $this->ajaxResponse(false, $validator->errors()->first());       
        }

        $credential = $request->only('username', 'password');
        $credential['status'] = 'active';
        if(Auth::attempt($credential)) {
            // $menu = $this->menu->menu();
            // $request->session()->push('menu', $menu);

            return $this->ajaxResponse(true, 'Berhasil masuk dashboard');
        } else {
            return $this->ajaxResponse(false, 'Username atau kata sandi salah. Silahkan cek kembali.');
        }
    }

    public function register() {
        $title = 'Register | Jari POS';
        $sliders = Campaign::where('type', 'slider')->where('status', 'active')->get();
        return view('auth.register', compact('title', 'sliders'));
    }

    public function processRegister(Request $request) {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        auth()->login($user);

        return redirect()->route('dashboard');
    }

    public function resetPassword() {
        return view('auth.reset-password');
    }

    public function processResetPassword(Request $request) {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'The provided email does not match our records.',
            ]);
        }

        $password = Str::random(8);
        $user->password = Hash::make($password);
        $user->save();

        Mail::to($user->email)->send(new ResetPasswordMail($user, $password));

        return redirect()->route('login');
    }

    public function logout() {
        auth()->logout();
        return redirect()->route('login');
    }
}
