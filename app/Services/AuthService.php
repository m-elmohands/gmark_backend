<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(private readonly UserRepositoryInterface $users) {}

    /** @param array{name: string, email: string, password: string} $data */
    public function register(array $data): User
    {
        return $this->users->create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => $data['password'],
            'is_active' => true,
            'is_admin' => false,
        ]);
    }

    public function validateCredentials(string $email, string $password): ?User
    {
        $user = $this->users->findByEmail(strtolower($email));

        if (! $user || ! $user->is_active || ! Hash::check($password, $user->password)) {
            return null;
        }

        $user->forceFill(['last_login_at' => now()])->save();

        return $user;
    }
}
