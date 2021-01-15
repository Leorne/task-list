<?php

namespace Infrastructure\Security;

use App\Entity\User\User;
use App\Entity\User\UserRepository;
use App\UseCase\User\Login\Command;
use Infrastructure\Services\User\PasswordHasher;

class Authenticator
{
    private UserRepository $users;
    private PasswordHasher $hasher;

    public function __construct(UserRepository $users, PasswordHasher $hasher)
    {
        $this->users = $users;
        $this->hasher = $hasher;
    }

    public function getUser(Command $credentials): User
    {
        $user = $this->users->findByName($credentials->name);
        if($user){
            return $user;
        }

        throw new \DomainException('User with such name not found.');
    }

    public function checkCredentials(Command $credentials, User $user): bool
    {
        return $this->hasher->validate($credentials->password, $user->getPasswordHash());
    }


}