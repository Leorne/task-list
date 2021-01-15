<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table (name="users")
 */
class User
{

    /**
     * @ORM\Column(type="users_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\Column (type="string", name="name", unique=true)
     */
    private string $name;

    /**
     * @ORM\Column (type="string", name="email")
     */
    private string $email;

    /**
     * @ORM\Column (type="string", name="password_hash")
     */
    private string $passwordHash;

    public function __construct(Id $id, string $name, string $email, string $passwordHash)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }
}