<?php

namespace App\Entity\Task;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table (name="tasks")
 */
class Task
{
    /**
     * @ORM\Column(type="tasks_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\Column (type="datetime_immutable")
     */
    private \DateTimeImmutable $date;

    /**
     * @ORM\Column (type="string", name="user_name")
     */
    private string $userName;

    /**
     * @ORM\Column (type="string", name="user_email")
     */
    private string $userEmail;

    /**
     * @ORM\Column (type="text")
     */
    private string $content;

    /**
     * @ORM\Column (type="boolean")
     */
    private bool $completed;

    /**
     * @ORM\Column (type="boolean")
     */
    private bool $edited;

    public function __construct(
        Id $id,
        \DateTimeImmutable $date,
        string $userName,
        string $userEmail,
        string $content
    )
    {
        $this->id = $id;
        $this->date = $date;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->content = $content;
        $this->completed = false;
        $this->edited = false;
    }

    public function editContent(string $content): void
    {
        $this->content = $content;
        $this->edited = true;
    }

    public function complete(): void
    {
        $this->completed = true;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCompleted(): bool
    {
        return $this->completed;
    }

    public function getEdited(): bool
    {
        return $this->edited;
    }
}