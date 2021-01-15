<?php

namespace Infrastructure\Security;

use App\Entity\User\User;
use App\Entity\User\UserRepository;
use Infrastructure\Services\User\PasswordHasher;

class UserProvider
{
    private UserRepository $users;
    private PasswordHasher $hasher;

    public function __construct(UserRepository $users, PasswordHasher $hasher)
    {
        $this->users = $users;
        $this->hasher = $hasher;
    }

    public function loadUser(array $credentials): User
    {
        $user = $this->users->findByName($credentials['name']);
        if ($user) {
            return $user;
        }

        throw new \DomainException('User with such credentials not found.');
    }

    public function checkCredentials(array $credentials, User $user): bool
    {
        return $this->hasher->isEqual($credentials['password'], $user->getPasswordHash());
    }


}