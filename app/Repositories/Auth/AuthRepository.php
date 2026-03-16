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
     * Create a new payment method
     */
    public static function createPaymentMethod(int $companyId): bool
    {
        $default = [
            ['name' => 'Tunai', 'type' => 'cash'],

            ['name' => 'Bank BCA', 'type' => 'bank_transfer'],
            ['name' => 'Bank BRI', 'type' => 'bank_transfer'],
            ['name' => 'Bank Mandiri', 'type' => 'bank_transfer'],
            ['name' => 'Bank BNI', 'type' => 'bank_transfer'],

            ['name' => 'GoPay', 'type' => 'e-wallet'],
            ['name' => 'OVO', 'type' => 'e-wallet'],
            ['name' => 'DANA', 'type' => 'e-wallet'],
            ['name' => 'ShopeePay', 'type' => 'e-wallet'],

            ['name' => 'QRIS', 'type' => 'other'],
        ];

        $data = [];
        foreach ($default as $method) {
            $data[] = array_merge($method, ['company_id' => $companyId]);
        }

        if (empty($data)) {
            return false;
        }

        $paymentMethod = PaymentMethod::insert($data);

        if (!$paymentMethod) {
            return false;
        }

        return $paymentMethod;
    }

    /**
     * Create a new unit
     */
    public static function createUnit(int $companyId): bool
    {
        $default = [
            ['code' => 'KG', 'name' => 'Kilogram'],
            ['code' => 'G', 'name' => 'Gram'],
            ['code' => 'MG', 'name' => 'Miligram'],
            ['code' => 'ONS', 'name' => 'Ons'],

            ['code' => 'L', 'name' => 'Liter'],
            ['code' => 'ML', 'name' => 'Mililiter'],

            ['code' => 'PCS', 'name' => 'Pieces'],
            ['code' => 'UNIT', 'name' => 'Unit'],
            ['code' => 'SET', 'name' => 'Set'],
            ['code' => 'PACK', 'name' => 'Pack'],
            ['code' => 'BOX', 'name' => 'Box'],
            ['code' => 'DUS', 'name' => 'Dus'],
            ['code' => 'LUSIN', 'name' => 'Lusin'],
            ['code' => 'KODI', 'name' => 'Kodi'],

            ['code' => 'M', 'name' => 'Meter'],
            ['code' => 'CM', 'name' => 'Centimeter'],

            ['code' => 'BTL', 'name' => 'Botol'],
            ['code' => 'SACHET', 'name' => 'Sachet'],
            ['code' => 'KALENG', 'name' => 'Kaleng'],
            ['code' => 'CUP', 'name' => 'Cup'],
            ['code' => 'PORSI', 'name' => 'Porsi'],
        ];

        $data = [];
        foreach ($default as $unit) {
            $data[] = array_merge($unit, ['company_id' => $companyId]);
        }

        if (empty($data)) {
            return false;
        }

        $unit = Unit::insert($data);    

        if (!$unit) {
            return false;
        }

        return $unit;
    }

    /**
     * Create a new category
     */
    public static function createCategory(int $companyId): bool
    {
        $default = [
            ['code' => 'MKN', 'name' => 'Makanan'],
            ['code' => 'MNM', 'name' => 'Minuman'],
            ['code' => 'SNK', 'name' => 'Snack'],
            ['code' => 'KUE', 'name' => 'Kue & Roti'],
            ['code' => 'SYR', 'name' => 'Sayur & Buah'],
            ['code' => 'FRZ', 'name' => 'Frozen Food'],
            ['code' => 'LNY', 'name' => 'Lainnya'],
        ];

        $data = [];
        foreach ($default as $category) {
            $data[] = array_merge($category, ['company_id' => $companyId]);
        }

        if (empty($data)) {
            return false;
        }

        $category = Category::insert($data);

        if (!$category) {
            return false;
        }

        return $category;
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
