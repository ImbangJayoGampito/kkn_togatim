<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    public static function loginUser(string $usernameOrEmail, string $password): ?User
    {
        if (empty($usernameOrEmail) || empty($password)) {
            throw new \InvalidArgumentException('Username or email and password are required.');
        }

        $user = User::where('email', $usernameOrEmail)
            ->orWhere('username', $usernameOrEmail)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new \Exception('Invalid credentials.');
        }

        return $user;
    }

    public static function registerUser(string $name, string $email, string $password): User
    {
        $passwordHashed = Hash::make($password);

        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => $passwordHashed,
        ])->assignRole('user');
    }
}
