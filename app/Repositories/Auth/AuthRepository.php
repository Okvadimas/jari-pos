<?php

namespace App\Repositories\Auth;

use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Str;

class AuthRepository
{
    /**
     * Find user by email
     */
    public static function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Find user by ID
     */
    public static function findUserById(int $id): ?User
    {
        return User::findOrFail($id);
    }

    /**
     * Create a new company
     */
    public static function createCompany(array $data): Company
    {
        return Company::create([
            'code'              => self::generateCompanyCode(),
            'name'              => $data['company_name'],
            'business_category' => $data['business_category'],
            'email'             => $data['company_email'],
            'phone'             => $data['company_phone'] ?? null,
            'address'           => $data['company_address'] ?? null,
        ]);
    }

    /**
     * Create a new user
     */
    public static function createUser(array $data, int $companyId): User
    {
        return User::create([
            'name'       => $data['name'],
            'username'   => $data['username'],
            'email'      => $data['email'],
            'password'   => $data['password'],
            'company_id' => $companyId,
            'start_date' => now()->toDateString(),
            'end_date'   => now()->addYear()->toDateString(),
        ]);
    }

    /**
     * Generate unique company code (e.g., "CMP-XXXXX")
     */
    public static function generateCompanyCode(): string
    {
        do {
            $code = 'CMP-' . strtoupper(Str::random(5));
        } while (Company::where('code', $code)->exists());

        return $code;
    }
}
