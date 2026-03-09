<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmailNotification;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup: seed roles & companies needed by FK constraints
     */
    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Super Admin', 'slug' => 'super-admin', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'User',        'slug' => 'user',        'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('companies')->insert([
            'id'   => 1,
            'code' => 'CMP-TEST1',
            'name' => 'Test Company',
            'email' => 'company@test.com',
            'business_category' => 'retail',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Helper: create a verified user
     */
    private function createVerifiedUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'company_id' => 1,
            'role_id'    => 2,
            'username'   => 'testuser',
            'password'   => Hash::make('password123'),
        ], $overrides));
    }

    /**
     * Helper: create an unverified user
     */
    private function createUnverifiedUser(array $overrides = []): User
    {
        return User::factory()->unverified()->create(array_merge([
            'company_id' => 1,
            'role_id'    => 2,
            'username'   => 'unverified_user',
            'password'   => Hash::make('password123'),
        ], $overrides));
    }

    /**
     * Helper: send AJAX POST (login needs ajax-request middleware)
     */
    private function ajaxPost(string $url, array $data = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson($url, $data, [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
    }

    // =========================================================================
    // LOGIN PAGE (GET)
    // =========================================================================

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_login_page_redirects_if_authenticated(): void
    {
        $user = $this->createVerifiedUser();
        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect();
    }

    // =========================================================================
    // LOGIN PROCESS (POST)
    // =========================================================================

    public function test_login_success_with_valid_credentials(): void
    {
        $user = $this->createVerifiedUser();

        $response = $this->ajaxPost('/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_success_with_email(): void
    {
        $user = $this->createVerifiedUser(['email' => 'testuser@example.com']);

        $response = $this->ajaxPost('/login', [
            'username' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_email(): void
    {
        $this->createVerifiedUser();

        $response = $this->ajaxPost('/login', [
            'username' => 'wrong@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => false]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->createVerifiedUser();

        $response = $this->ajaxPost('/login', [
            'username' => 'testuser',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => false]);
    }

    public function test_login_fails_with_wrong_username(): void
    {
        $this->createVerifiedUser();

        $response = $this->ajaxPost('/login', [
            'username' => 'nonexistent_user',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => false]);
    }

    public function test_login_fails_with_empty_credentials(): void
    {
        $response = $this->ajaxPost('/login', []);

        $response->assertStatus(422);
        $response->assertJson(['status' => false]);
    }

    public function test_login_fails_with_empty_username(): void
    {
        $response = $this->ajaxPost('/login', [
            'username' => '',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_fails_with_empty_password(): void
    {
        $response = $this->ajaxPost('/login', [
            'username' => 'testuser',
            'password' => '',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_fails_for_unverified_email(): void
    {
        $this->createUnverifiedUser();

        $response = $this->ajaxPost('/login', [
            'username' => 'unverified_user',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => false]);
        $this->assertGuest();
    }

    public function test_login_requires_ajax_request(): void
    {
        $this->createVerifiedUser();

        // Non-AJAX POST should be rejected by ajax-request middleware
        $response = $this->post('/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(404);
    }

    // =========================================================================
    // REGISTER PAGE (GET)
    // =========================================================================

    public function test_register_page_is_accessible(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    // =========================================================================
    // REGISTER PROCESS (POST)
    // =========================================================================

    public function test_register_success_with_valid_data(): void
    {
        Notification::fake();

        $response = $this->postJson('/register', [
            'name'              => 'Test User Register',
            'username'          => 'newuser123',
            'email'             => 'newuser@test.com',
            'password'          => 'pass1234',
            'company_name'      => 'New Company',
            'business_category' => 'retail',
            'company_email'     => 'newcompany@test.com',
            'company_phone'     => '08123456789',
            'company_address'   => 'Jl. Test No. 1',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        // Verify user created in DB
        $this->assertDatabaseHas('users', [
            'username' => 'newuser123',
            'email'    => 'newuser@test.com',
        ]);

        // Verify company created in DB
        $this->assertDatabaseHas('companies', [
            'name'  => 'New Company',
            'email' => 'newcompany@test.com',
        ]);

        // Verify password is hashed (not stored as plaintext)
        $user = User::where('username', 'newuser123')->first();
        $this->assertTrue(Hash::check('pass1234', $user->password));
        $this->assertNotEquals('pass1234', $user->password);

        // Verify email verification notification sent
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    /**
     * REAL REGISTER TEST: actually sends email to thinkerdust.dev@gmail.com
     * Run this test individually: php artisan test --filter=test_register_sends_real_email
     *
     * NOTE: phpunit.xml sets MAIL_MAILER=array. This test overrides it to use SMTP.
     */
    public function test_register_sends_real_email(): void
    {
        // Force SMTP mailer (override phpunit.xml MAIL_MAILER=array)
        config([
            'mail.default'                => 'smtp',
            'mail.mailers.smtp.host'      => env('MAIL_HOST', 'mail.ocsabron.com'),
            'mail.mailers.smtp.port'      => env('MAIL_PORT', 465),
            'mail.mailers.smtp.username'   => env('MAIL_USERNAME', 'jaripos@ocsabron.com'),
            'mail.mailers.smtp.password'   => env('MAIL_PASSWORD'),
            'mail.mailers.smtp.scheme'     => env('MAIL_SCHEME'),
        ]);

        // DON'T fake notifications – let real email send
        $response = $this->postJson('/register', [
            'name'              => 'Thinkerdust Test',
            'username'          => 'thinkerdust_test',
            'email'             => 'thinkerdust.dev@gmail.com',
            'password'          => 'test1234',
            'company_name'      => 'Thinkerdust Company',
            'business_category' => 'retail',
            'company_email'     => 'thinkerdust.company@gmail.com',
            'company_phone'     => '081234567890',
            'company_address'   => 'Jl. Testing Email',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'thinkerdust.dev@gmail.com',
        ]);
    }

    public function test_register_fails_without_required_fields(): void
    {
        $response = $this->postJson('/register', []);

        $response->assertStatus(422);
        $response->assertJson(['status' => false]);
    }

    public function test_register_fails_with_duplicate_username(): void
    {
        $this->createVerifiedUser(['username' => 'existinguser']);

        $response = $this->postJson('/register', [
            'name'              => 'Duplicate User',
            'username'          => 'existinguser',
            'email'             => 'unique@test.com',
            'password'          => 'pass1234',
            'company_name'      => 'Some Company',
            'business_category' => 'retail',
            'company_email'     => 'unique_company@test.com',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => false]);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        $this->createVerifiedUser(['email' => 'taken@test.com']);

        $response = $this->postJson('/register', [
            'name'              => 'Dup Email User',
            'username'          => 'uniqueuser',
            'email'             => 'taken@test.com',
            'password'          => 'pass1234',
            'company_name'      => 'Another Company',
            'business_category' => 'retail',
            'company_email'     => 'another@test.com',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => false]);
    }

    public function test_register_fails_with_duplicate_company_email(): void
    {
        // company@test.com already exists from setUp()
        $response = $this->postJson('/register', [
            'name'              => 'New User',
            'username'          => 'newuser',
            'email'             => 'newuser@test.com',
            'password'          => 'pass1234',
            'company_name'      => 'Dup Company',
            'business_category' => 'retail',
            'company_email'     => 'company@test.com',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => false]);
    }

    public function test_register_fails_with_invalid_business_category(): void
    {
        $response = $this->postJson('/register', [
            'name'              => 'Bad Category',
            'username'          => 'badcat',
            'email'             => 'badcat@test.com',
            'password'          => 'pass1234',
            'company_name'      => 'Bad Category Co',
            'business_category' => 'invalid_category',
            'company_email'     => 'badcat_co@test.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_register_fails_with_short_password(): void
    {
        $response = $this->postJson('/register', [
            'name'              => 'Short Pass',
            'username'          => 'shortpass',
            'email'             => 'short@test.com',
            'password'          => 'abc',  // min:4
            'company_name'      => 'Short Pass Co',
            'business_category' => 'retail',
            'company_email'     => 'shortco@test.com',
        ]);

        $response->assertStatus(422);
    }

    public function test_register_fails_with_invalid_email_format(): void
    {
        $response = $this->postJson('/register', [
            'name'              => 'Bad Email',
            'username'          => 'bademail',
            'email'             => 'not-an-email',
            'password'          => 'pass1234',
            'company_name'      => 'Bad Email Co',
            'business_category' => 'retail',
            'company_email'     => 'valid@company.com',
        ]);

        $response->assertStatus(422);
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================

    public function test_logout_redirects_to_login(): void
    {
        $user = $this->createVerifiedUser();

        $response = $this->actingAs($user)->get('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    // =========================================================================
    // SECURITY: SQL INJECTION TESTS
    // =========================================================================

    public function test_login_is_safe_from_sql_injection_in_username(): void
    {
        $this->createVerifiedUser();

        $sqliPayloads = [
            "' OR '1'='1",
            "' OR '1'='1' --",
            "' OR '1'='1' /*",
            "admin'--",
            "' UNION SELECT * FROM users --",
            "1'; DROP TABLE users; --",
            "' OR 1=1#",
            "testuser' AND 1=1 --",
        ];

        foreach ($sqliPayloads as $payload) {
            $response = $this->ajaxPost('/login', [
                'username' => $payload,
                'password' => 'password123',
            ]);

            // Should NOT authenticate (422 = invalid credentials, 429 = rate limited — both are safe)
            $this->assertContains($response->status(), [422, 429],
                "SQL injection payload should not bypass login: {$payload}");
            $this->assertGuest();
        }
    }

    public function test_login_is_safe_from_sql_injection_in_password(): void
    {
        $this->createVerifiedUser();

        $sqliPayloads = [
            "' OR '1'='1",
            "' OR '1'='1' --",
            "password' OR '1'='1",
            "1' OR '1'='1' UNION SELECT * FROM users --",
        ];

        foreach ($sqliPayloads as $payload) {
            $response = $this->ajaxPost('/login', [
                'username' => 'testuser',
                'password' => $payload,
            ]);

            // 422 = invalid credentials, 429 = rate limited — both are safe
            $this->assertContains($response->status(), [422, 429],
                "SQL injection in password should not bypass login: {$payload}");
            $this->assertGuest();
        }
    }

    public function test_register_is_safe_from_sql_injection(): void
    {
        Notification::fake();

        $sqliPayloads = [
            "'; DROP TABLE users; --",
            "' UNION SELECT * FROM users --",
            "1' OR '1'='1",
        ];

        foreach ($sqliPayloads as $payload) {
            $uniqueSuffix = md5($payload);

            $response = $this->postJson('/register', [
                'name'              => $payload,
                'username'          => "sqli_user_{$uniqueSuffix}",
                'email'             => "sqli_{$uniqueSuffix}@test.com",
                'password'          => 'pass1234',
                'company_name'      => $payload,
                'business_category' => 'retail',
                'company_email'     => "sqli_co_{$uniqueSuffix}@test.com",
            ]);

            // If registration succeeds, name should be stored as literal string, not executed
            if ($response->status() === 200) {
                $this->assertDatabaseHas('users', [
                    'name' => $payload,
                ]);
            }

            // Verify users table still exists
            $this->assertDatabaseCount('roles', 2);
        }
    }

    // =========================================================================
    // SECURITY: XSS TESTS
    // =========================================================================

    public function test_register_stores_xss_as_plain_text(): void
    {
        Notification::fake();

        $xssPayload = '<script>alert("XSS")</script>';

        $response = $this->postJson('/register', [
            'name'              => $xssPayload,
            'username'          => 'xss_test_user',
            'email'             => 'xss@test.com',
            'password'          => 'pass1234',
            'company_name'      => $xssPayload,
            'business_category' => 'retail',
            'company_email'     => 'xss_co@test.com',
        ]);

        $response->assertStatus(200);

        // Data should be stored as literal text, not executed
        $user = User::where('username', 'xss_test_user')->first();
        $this->assertEquals($xssPayload, $user->name);
    }

    // =========================================================================
    // SECURITY: BRUTE FORCE / MASS ASSIGNMENT
    // =========================================================================

    public function test_register_cannot_set_role_id_via_mass_assignment(): void
    {
        Notification::fake();

        $response = $this->postJson('/register', [
            'name'              => 'Hacker User',
            'username'          => 'hacker_user',
            'email'             => 'hacker@test.com',
            'password'          => 'pass1234',
            'company_name'      => 'Hacker Co',
            'business_category' => 'retail',
            'company_email'     => 'hackerco@test.com',
            'role_id'           => 1,  // Try to assign Super Admin
        ]);

        if ($response->status() === 200) {
            $user = User::where('username', 'hacker_user')->first();
            // role_id should default to 2 (User), not 1 (Super Admin)
            $this->assertEquals(2, $user->role_id,
                'User should not be able to assign themselves Super Admin role');
        }
    }

    public function test_register_cannot_set_email_verified_via_mass_assignment(): void
    {
        Notification::fake();

        $response = $this->postJson('/register', [
            'name'              => 'Verify Hacker',
            'username'          => 'verify_hacker',
            'email'             => 'verifyhack@test.com',
            'password'          => 'pass1234',
            'company_name'      => 'Verify Hack Co',
            'business_category' => 'retail',
            'company_email'     => 'verifyhackco@test.com',
            'email_verified_at' => now(),  // Try to bypass email verification
        ]);

        if ($response->status() === 200) {
            $user = User::where('username', 'verify_hacker')->first();
            $this->assertNull($user->email_verified_at,
                'User should not be able to bypass email verification');
        }
    }

    // =========================================================================
    // EMAIL VERIFICATION
    // =========================================================================

    public function test_verify_email_page_is_accessible(): void
    {
        $user = $this->createUnverifiedUser();

        $response = $this->actingAs($user)->get('/email/verify');
        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
    }

    public function test_verify_email_with_valid_hash(): void
    {
        $user = $this->createUnverifiedUser();
        $hash = sha1($user->getEmailForVerification());

        $response = $this->get("/email/verify/{$user->id}/{$hash}");

        $response->assertRedirect(route('login'));
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_verify_email_with_invalid_hash(): void
    {
        $user = $this->createUnverifiedUser();

        $response = $this->get("/email/verify/{$user->id}/invalidhash123");

        $response->assertRedirect(route('login'));
        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_resend_verification_email(): void
    {
        Notification::fake();

        $user = $this->createUnverifiedUser(['email' => 'resend@test.com']);

        $response = $this->postJson('/email/verification-resend', [
            'email' => 'resend@test.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_resend_verification_fails_for_unknown_email(): void
    {
        $response = $this->postJson('/email/verification-resend', [
            'email' => 'unknown@test.com',
        ]);

        $response->assertStatus(404);
        $response->assertJson(['status' => false]);
    }

    // =========================================================================
    // ENSUREEMAILISVERIFIED MIDDLEWARE
    // =========================================================================

    public function test_verified_user_can_access_dashboard(): void
    {
        $user = $this->createVerifiedUser();

        $response = $this->actingAs($user)->get('/dashboard');

        // Should not redirect to verification notice
        $response->assertStatus(200);
    }

    public function test_unverified_user_cannot_access_dashboard(): void
    {
        $user = $this->createUnverifiedUser();

        $response = $this->actingAs($user)->get('/dashboard');

        // Should redirect to email verification notice
        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_user_cannot_access_pos(): void
    {
        $user = $this->createUnverifiedUser();

        $response = $this->actingAs($user)->get('/pos');

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_unverified_user_can_access_logout(): void
    {
        $user = $this->createUnverifiedUser();

        // logout is outside the verified middleware group, so unverified users can logout
        $response = $this->actingAs($user)->get('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    // =========================================================================
    // RATE LIMITING / BRUTE FORCE PROTECTION
    // =========================================================================

    public function test_login_is_rate_limited_after_too_many_attempts(): void
    {
        $this->createVerifiedUser();

        // Make 5 failed login attempts (limit is 5 per minute)
        for ($i = 0; $i < 5; $i++) {
            $response = $this->ajaxPost('/login', [
                'username' => 'testuser',
                'password' => 'wrong_password',
            ]);

            $response->assertStatus(422);
        }

        // 6th attempt should be throttled (429 Too Many Requests)
        $response = $this->ajaxPost('/login', [
            'username' => 'testuser',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(429);
    }

    public function test_reset_password_is_rate_limited(): void
    {
        // Make 5 reset password attempts (limit is 5 per minute)
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/reset-password', [
                'email' => 'anyone@test.com',
            ]);
        }

        // 6th attempt should be throttled
        $response = $this->postJson('/reset-password', [
            'email' => 'anyone@test.com',
        ]);

        $response->assertStatus(429);
    }

    public function test_login_rate_limit_does_not_block_valid_login_within_limit(): void
    {
        $user = $this->createVerifiedUser();

        // Make 4 failed attempts (under limit of 5)
        for ($i = 0; $i < 4; $i++) {
            $this->ajaxPost('/login', [
                'username' => 'testuser',
                'password' => 'wrong_password',
            ]);
        }

        // 5th attempt with correct password should still work
        $response = $this->ajaxPost('/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $this->assertAuthenticatedAs($user);
    }
}
