<?php

namespace Infrastructure\Services\User;

class PasswordHasher implements PasswordHasherInterface
{
    public function hash(string $password): string
    {
        $hash = password_hash($password, PASSWORD_ARGON2I);
        if ($hash === false) {
            throw new \RuntimeException('Unable to generate hash');
        }
        return $hash;
    }

    public function validate(string $password, string $passwordHash): bool
    {
        return password_verify($password, $passwordHash);
    }

    public function isEqual(string $passwordHash, string $anotherPasswordHash): bool
    {
        return $passwordHash === $anotherPasswordHash;
    }
}