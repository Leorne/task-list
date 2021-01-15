<?php

namespace App\UseCase\Tasks\Edit;

use App\Entity\Task\Task;

class Command
{
    public string $id;
    public string $content;

    static public function fromTask(Task $task): self
    {
        $command = new self();
        $command->id = $task->getId()->getValue();
        $command->content = $task->getContent();

        return $command;
    }
}