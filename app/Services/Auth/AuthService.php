<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

// Load Repository
use App\Repositories\Auth\AuthRepository;

// Load Models
use App\Models\User;

// Load Service
use App\Services\Management\MenuService;

class AuthService
{
    /**
     * Handle user registration: create company + user + send verification email
     *
     * @param array $validated
     * @return array ['status' => bool, 'message' => string]
     */
    public static function register(array $validated): array
    {
        try {
            DB::beginTransaction();

            // Create Company
            $company = AuthRepository::createCompany($validated);

            // Create User
            $validated['password'] = Hash::make($validated['password']);
            $user = AuthRepository::createUser($validated, $company->id);

            DB::commit();

            // Send verification email notification
            $user->sendEmailVerificationNotification();

            return [
                'status'  => true,
                'message' => 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi.',
            ];

        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Registration failed: ' . $th->getMessage());
            return [
                'status'  => false,
                'message' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.',
            ];
        }
    }

    /**
     * Handle user login with email verification check
     *
     * @param array $credentials
     * @param Request $request
     * @return array ['status' => bool, 'message' => string]
     */
    public static function login(array $credentials, Request $request): array
    {
        if (!Auth::attempt($credentials)) {
            return [
                'status'  => false,
                'message' => 'Username atau kata sandi salah. Silahkan cek kembali.',
            ];
        }

        // Check email verification
        if (!Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            return [
                'status'  => false,
                'message' => 'Email belum diverifikasi. Silakan cek email Anda untuk verifikasi.',
            ];
        }

        // Setup session
        $request->session()->regenerate();
        $request->session()->put('role_slug', Auth::user()->role->slug);
        $request->session()->put('company_id', Auth::user()->company_id);
        $request->session()->put('company_code', Auth::user()->company->code);

        $menu = MenuService::generateMenu();
        $request->session()->put('menu', $menu);

        return [
            'status'  => true,
            'message' => 'Berhasil masuk dashboard',
        ];
    }

    /**
     * Handle email verification
     *
     * @param int $id
     * @param string $hash
     * @return array ['status' => bool, 'message' => string, 'type' => string]
     */
    public static function verifyEmail(int $id, string $hash): array
    {
        $user = AuthRepository::findUserById($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return [
                'status'  => false,
                'message' => 'Link verifikasi tidak valid.',
                'type'    => 'error',
            ];
        }

        if ($user->hasVerifiedEmail()) {
            return [
                'status'  => true,
                'message' => 'Email sudah diverifikasi sebelumnya.',
                'type'    => 'info',
            ];
        }

        $user->markEmailAsVerified();

        return [
            'status'  => true,
            'message' => 'Email berhasil diverifikasi! Silakan login.',
            'type'    => 'success',
        ];
    }

    /**
     * Resend email verification
     *
     * @param string $email
     * @return array ['status' => bool, 'message' => string]
     */
    public static function resendVerification(string $email): array
    {
        $user = AuthRepository::findUserByEmail($email);

        if (!$user) {
            return [
                'status'  => false,
                'message' => 'Email tidak ditemukan.',
            ];
        }

        if ($user->hasVerifiedEmail()) {
            return [
                'status'  => true,
                'message' => 'Email sudah diverifikasi. Silakan login.',
            ];
        }

        $user->sendEmailVerificationNotification();

        return [
            'status'  => true,
            'message' => 'Email verifikasi berhasil dikirim ulang.',
        ];
    }

    /**
     * Handle password reset
     *
     * @param string $email
     * @return array ['status' => bool, 'message' => string]
     */
    public static function resetPassword(string $email): array
    {
        $user = AuthRepository::findUserByEmail($email);

        if (!$user) {
            return [
                'status'  => false,
                'message' => 'Email tidak ditemukan dalam sistem kami.',
            ];
        }

        $password = \Illuminate\Support\Str::random(8);
        $user->password = Hash::make($password);
        $user->save();

        \Illuminate\Support\Facades\Mail::to($user->email)
            ->send(new \App\Mail\ResetPasswordMail($user, $password));

        return [
            'status'  => true,
            'message' => 'Kata sandi baru telah dikirim ke email Anda.',
        ];
    }
}
