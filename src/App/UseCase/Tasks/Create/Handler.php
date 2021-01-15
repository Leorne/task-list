<?php

namespace App\UseCase\Tasks\Create;

use App\Entity\Task\Id;
use App\Entity\Task\Task;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Services\Flusher;

class Handler
{
    private EntityManagerInterface $em;
    private Flusher $flusher;

    public function __construct(EntityManagerInterface $em, Flusher $flusher)
    {
        $this->em = $em;
        $this->flusher = $flusher;
    }

    public function handle(Command $command) : void
    {
        $task = new Task(
            Id::next(),
            new \DateTimeImmutable(),
            $command->userName,
            $command->userEmail,
            $command->content
        );

        $this->em->persist($task);
        $this->flusher->flush();
    }
}