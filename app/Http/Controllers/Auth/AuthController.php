<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Load Service
use App\Services\Auth\AuthService;

// Load Request
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

// Load Model
use App\Models\Campaign;
use App\Models\User;

class AuthController extends Controller
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
        Auth::logout();
        return redirect()->route('login');
    }

    public function myProfile()
    {
        $title = 'Profil Saya | Jari POS';
        $css   = 'resources/css/pages/auth/my-profile.css';
        $js    = 'resources/js/pages/auth/my-profile.js';
        $user  = Auth::user();

        return view('auth.my-profile', compact('title', 'css', 'js', 'user'));
    }
    public function getProfileData()
    {
        $user = User::where('users.id', Auth::user()->id)
                    ->join('roles', 'users.role_id', '=', 'roles.id')
                    ->join('companies', 'users.company_id', '=', 'companies.id')
                    ->select('users.name', 'users.username', 'users.email', 'users.phone', 'users.birth_date', 'users.address', 'users.profile_picture', 'users.last_login', 'users.start_date', 'users.end_date', 'roles.name as role', 'companies.name as company')->first();

        return response()->json([
            'status' => true,
            'data'   => $user,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string',
        ]);

        $user->update([
            'name'       => $request->name,
            'phone'      => $request->phone,
            'birth_date' => $request->birth_date ? formatTanggalDatabase($request->birth_date) : null,
            'address'    => $request->address,
        ]);

        return $this->successResponse('Profil berhasil diperbarui');
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
        ]);

        $user = Auth::user();
        $imageParts = explode(";base64,", $request->image);
        
        if (count($imageParts) >= 2) {
            $imageTypeAux = explode("image/", $imageParts[0]);
            $imageType = $imageTypeAux[1];
            $imageBase64 = base64_decode($imageParts[1]);
            
            $fileName = 'profile_' . $user->id . '_' . time() . '.' . $imageType;
            $dirPath = public_path('uploads/profiles');
            $fullPath = $dirPath . '/' . $fileName;
            
            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }
            
            file_put_contents($fullPath, $imageBase64);
            
            // Delete old picture if exists and not default
            if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
                unlink(public_path($user->profile_picture));
            }
            
            $user->update([
                'profile_picture' => 'uploads/profiles/' . $fileName
            ]);
            
            return $this->successResponse('Foto profil berhasil diperbarui', ['url' => asset('uploads/profiles/' . $fileName)]);
        }

        return $this->errorResponse('Format gambar tidak valid', 400);
    }

    public function lockScreen()
    {
        session(['locked' => true]);

        $title = 'Layar Terkunci | Jari POS';
        $css   = 'resources/css/pages/auth/lock-screen.css';
        $js    = 'resources/js/pages/auth/lock-screen.js';

        return view('auth.lock-screen', compact('title', 'css', 'js'));
    }

    public function unlockScreen(Request $request)
    {
        $request->validate(['password' => 'required']);

        if (Hash::check($request->password, Auth::user()->password)) {
            session()->forget('locked');
            return $this->successResponse('Berhasil membuka kunci layar');
        }

        return $this->errorResponse('Kata sandi salah', 422);
    }

    public function changePassword()
    {
        $title = 'Ubah Kata Sandi | Jari POS';
        $css   = 'resources/css/pages/auth/change-password.css';
        $js    = 'resources/js/pages/auth/change-password.js';

        return view('auth.change-password', compact('title', 'css', 'js'));
    }

    public function processChangePassword(\App\Http\Requests\Auth\ChangePasswordRequest $request)
    {
        $result = AuthService::changePassword($request->validated());

        return $result['status']
            ? $this->successResponse($result['message'])
            : $this->errorResponse($result['message'], 422);
    }
}
