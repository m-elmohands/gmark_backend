<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class ProfileService
{
    public function __construct(private readonly UserRepositoryInterface $users) {}

    /** @param array{name: string, email: string} $data */
    public function update(User $user, array $data): User
    {
        return $this->users->update($user, [
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'email_verified_at' => strtolower($data['email']) === $user->email ? $user->email_verified_at : null,
        ]);
    }

    public function changePassword(User $user, string $password): User
    {
        return $this->users->update($user, ['password' => $password]);
    }
}
