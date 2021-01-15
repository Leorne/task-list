<?php

namespace App\UseCase\User\Login;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $name;

    /**
     * @Assert\NotBlank
     */
    public string $password;
}