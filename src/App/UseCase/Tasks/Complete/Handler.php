<?php

namespace App\UseCase\Tasks\Complete;

use App\Entity\Task\Id;
use App\Entity\Task\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Services\Flusher;

class Handler
{
    private TaskRepository $tasks;
    private EntityManagerInterface $em;
    private Flusher $flusher;

    public function __construct(TaskRepository $tasks, EntityManagerInterface $em, Flusher $flusher)
    {
        $this->tasks = $tasks;
        $this->em = $em;
        $this->flusher = $flusher;
    }

    public function handle(Command $command) : void
    {
        $task = $this->tasks->get(new Id($command->id));
        $task->complete();

        $this->em->persist($task);
        $this->flusher->flush();
    }
}