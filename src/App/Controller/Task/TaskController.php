<?php

namespace App\Controller\Task;

use App\Controller\AbstractController;
use App\Entity\Task\Id;
use App\Entity\Task\TaskRepository;
use App\ReadModel\Task\TaskFetcher;
use App\UseCase\Tasks\Create;
use App\UseCase\Tasks\Edit;
use App\UseCase\Tasks\Complete;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TaskController extends AbstractController
{
    private const PER_PAGE = 3;

    private TaskFetcher $tasks;

    public function __construct(TaskFetcher $tasks)
    {
        $this->tasks = $tasks;
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();

        $pagination = $this->tasks->all(
            $query['page'] ?? 1,
            self::PER_PAGE,
            $query['sort'] ?? 'user_name',
            $query['direction'] ?? 'asc',
        );

        return $this->render('task/index', [
            'pagination' => $pagination,
        ]);
    }

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        $command = new Create\Command();

        $form = $this->createForm(Create\Form::class, $command);
        $form->handleRequest();

        if ($form->isSubmitted() && $form->isValid()) {
            $handler = $this->container->get(Create\Handler::class);
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Task successfully created.');
                return $this->redirect('/');
            } catch (\DomainException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('task/create', [
            'form' => $form->createView()
        ]);
    }

    public function edit(ServerRequestInterface $request): ResponseInterface
    {
        $taskId = $request->getAttribute('id');
        $repository = $this->container->get(TaskRepository::class);
        $task = $repository->get(new Id($taskId));
        $command = Edit\Command::fromTask($task);

        $form = $this->createForm(Edit\Form::class, $command);
        $form->handleRequest();

        if ($form->isSubmitted() && $form->isValid()) {
            $handler = $this->container->get(Edit\Handler::class);
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Task successfully edited.');
                return $this->redirect('/');
            } catch (\DomainException $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('task/edit', [
            'form' => $form->createView(),
            'taskId' => $command->id
        ]);
    }

    public function complete(ServerRequestInterface $request): ResponseInterface
    {
        $command = new Complete\Command();
        $command->id = $request->getAttribute('id');
        $handler = $this->container->get(Complete\Handler::class);

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Task completed.');
        } catch (\DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirect('/');
    }
}