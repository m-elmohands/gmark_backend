<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private const SPA_HEADERS = [
        'Accept-Language' => 'en',
        'Origin' => 'http://localhost:3000',
    ];

    public function test_public_user_can_register_without_admin_access(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Public Resident',
            'email' => 'resident@example.com',
            'password' => 'Secure123',
            'password_confirmation' => 'Secure123',
        ], self::SPA_HEADERS);

        $response->assertCreated()
            ->assertJsonPath('data.email', 'resident@example.com');

        $user = User::query()->where('email', 'resident@example.com')->firstOrFail();
        $this->assertFalse($user->is_admin);
        $this->assertFalse($user->canAccessPanel(Filament::getPanel('admin')));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_read_profile_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'resident@example.com',
            'password' => 'Secure123',
            'is_admin' => false,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'Secure123',
        ], self::SPA_HEADERS)->assertOk();

        $this->getJson('/api/v1/auth/me', self::SPA_HEADERS)
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);

        $this->postJson('/api/v1/auth/logout', [], self::SPA_HEADERS)->assertOk();
        $this->getJson('/api/v1/auth/me', self::SPA_HEADERS)->assertUnauthorized();
    }

    public function test_invalid_login_is_rejected(): void
    {
        User::factory()->create(['email' => 'resident@example.com', 'password' => 'Secure123']);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'resident@example.com',
            'password' => 'wrong-password',
        ], self::SPA_HEADERS)->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_auth_validation_errors_follow_the_requested_locale(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ], [...self::SPA_HEADERS, 'Accept-Language' => 'ar'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.name.0', 'حقل الاسم مطلوب.')
            ->assertJsonPath('errors.email.0', 'يجب أن يكون حقل البريد الإلكتروني بريداً إلكترونياً صحيحاً.')
            ->assertJsonPath('errors.password.0', 'تأكيد حقل كلمة المرور غير متطابق.');
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'resident@example.com', 'password' => 'Secure123']);
        $token = Password::createToken($user);

        $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'Changed123',
            'password_confirmation' => 'Changed123',
        ], self::SPA_HEADERS)->assertOk();

        $this->assertTrue(Hash::check('Changed123', $user->fresh()->password));
    }

    public function test_authenticated_user_can_update_profile_and_password(): void
    {
        $user = User::factory()->create([
            'email' => 'resident@example.com',
            'password' => 'Secure123',
            'is_admin' => false,
        ]);
        $this->actingAs($user);

        $this->putJson('/api/v1/auth/profile', [
            'name' => 'Updated Resident',
            'email' => 'updated@example.com',
        ], self::SPA_HEADERS)
            ->assertOk()
            ->assertJsonPath('data.name', 'Updated Resident')
            ->assertJsonPath('data.email', 'updated@example.com');

        $this->putJson('/api/v1/auth/password', [
            'current_password' => 'Secure123',
            'password' => 'Changed123',
            'password_confirmation' => 'Changed123',
        ], self::SPA_HEADERS)->assertOk();

        $this->assertTrue(Hash::check('Changed123', $user->fresh()->password));
    }

    public function test_password_change_rejects_an_incorrect_current_password_in_arabic(): void
    {
        $user = User::factory()->create(['password' => 'Secure123']);
        $this->actingAs($user);

        $this->putJson('/api/v1/auth/password', [
            'current_password' => 'Incorrect123',
            'password' => 'Changed123',
            'password_confirmation' => 'Changed123',
        ], [...self::SPA_HEADERS, 'Accept-Language' => 'ar'])
            ->assertUnprocessable()
            ->assertJsonPath('errors.current_password.0', 'كلمة المرور الحالية غير صحيحة.');
    }
}
