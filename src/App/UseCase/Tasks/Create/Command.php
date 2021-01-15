<?php

namespace App\UseCase\Tasks\Create;

use Symfony\Component\Validator\Constraints;

class Command
{
    /**
     * @Symfony\Component\Validator\Constraints\NotBlank()
     */
    public string $userName;

    /**
     * @Symfony\Component\Validator\Constraints\Email()
     * @Symfony\Component\Validator\Constraints\NotBlank()
     */
    public string $userEmail;

    /**
     * @Symfony\Component\Validator\Constraints\NotBlank
     */
    public string $content;

}