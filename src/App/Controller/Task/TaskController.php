<?php

namespace App\Controller\Task;

use App\Controller\AbstractController;
use App\Entity\Task\TaskRepository;
use App\ReadModel\Task\TaskFetcher;
use App\UseCase\Tasks\Create;
use App\UseCase\Tasks\Edit;
use App\UseCase\Tasks\Complete;
use Knp\Component\Pager\PaginatorInterface;
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

    public function index(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $repo = $this->container->get(TaskRepository::class);

        $tasks = $repo->all();
        $paginator = $this->container->get(PaginatorInterface::class);
        $query = $request->getQueryParams();

        $options = [];
        $params = [];

        $pagination = $this->tasks->all(
            $query['page'] ?? 1,
            self::PER_PAGE,
            $query['sort'] ?? 'user_name',
            $query['direction'] ?? 'asc',
        );

        return $this->render('task/index', [
            'pagination' => $pagination,
            'options' => $options,
            'params' => $params
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
        $command = new Edit\Command();
        $command->id = $request->getAttribute('id');

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