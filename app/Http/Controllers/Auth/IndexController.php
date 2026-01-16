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
use App\Models\Menu;

class IndexController extends Controller
{
    public function login() {
        $title      = 'Login | Jari POS';
        $sliders    = Campaign::where('type', 'slider')->where('status', 'active')->get();  
        $css        = 'resources/css/pages/auth/login.css';
        $js         = 'resources/js/pages/auth/login.js';  
        return view('auth.login', compact('css', 'js', 'title', 'sliders'));
    }

    public function processLogin(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Kata Sandi wajib diisi',
        ]);
   
        if($validator->stopOnFirstFailure()->fails()){
            return $this->errorResponse($validator->errors()->first());       
        }

        $credential = $request->only('username', 'password');
        $credential['status'] = 1;
        if(Auth::attempt($credential)) {
            $user = Auth::user();
            $request->session()->put('user', $user);
            $request->session()->put('role', $user->role->slug);
            $request->session()->put('company', $user->company);
            
            $menu = Menu::menu();
            $request->session()->put('menu', $menu);

            return $this->successResponse('Berhasil masuk dashboard');
        } else {
            return $this->errorResponse('Username atau kata sandi salah. Silahkan cek kembali.');
        }
    }

    public function register() {
        $title      = 'Register | Jari POS';
        $sliders    = Campaign::where('type', 'slider')->where('status', 'active')->get();
        $css        = 'resources/css/pages/auth/register.css';
        $js         = 'resources/js/pages/auth/register.js';  
        return view('auth.register', compact('css', 'js', 'title', 'sliders'));
    }

    public function processRegister(Request $request) {
        $validated = $request->validate([
            'name'      => 'required',
            'username'  => 'required',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:4',
        ], [
            'name.required'     => 'Nama Lengkap wajib diisi',
            'username.required' => 'Username wajib diisi',
            'email.required'    => 'Email wajib diisi',
            'email.email'       => 'Email tidak valid',
            'email.unique'      => 'Email sudah terdaftar',
            'password.required' => 'Kata Sandi wajib diisi',
            'password.min'      => 'Kata Sandi minimal 4 karakter',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'status'    => 'active',
        ]);

        // Auto Login
        Auth::login($user);

        return $this->successResponse('Berhasil mendaftar');
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
        Auth::logout();
        return redirect()->route('login');
    }
}
