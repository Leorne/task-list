<?php

namespace App\Entity\Task;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class TaskRepository
{
    private EntityManagerInterface $em;
    private ObjectRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $this->em->getRepository(Task::class);
    }

    public function get(Id $id): Task
    {
        /**
         * @var Task $task ;
         */
        if (!$task = $this->repo->find($id->getValue())) {
            throw new \DomainException('Task not found.');
        }
        return $task;
    }

    public function all(): array
    {
        return $this->repo->findAll();
    }
}