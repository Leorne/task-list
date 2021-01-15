<?php

namespace Infrastructure\Security;

use App\Entity\User\User;

interface SecurityInterface
{
    public function isAuth(): bool;

    public function getUser(): ?User;

    public function loginAs(User $user): void;

    public function logout(): void;
}