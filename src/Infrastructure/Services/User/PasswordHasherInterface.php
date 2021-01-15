<?php

namespace Infrastructure\Services\User;

interface PasswordHasherInterface
{
    public function hash(string $password) : string;

    public function validate(string $password, string $passwordHash) : bool;
}