<?php

namespace App\ReadModel\Task;

use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class TaskFetcher
{
    private Connection $connection;
    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function getTasks(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                't.id',
                't.user_name as name',
                't.user_email as email',
                't.content',
                't.completed',
                't.edited',
                't.date',
            )
            ->from('tasks', 't')
            ->orderBy('t.date')->addOrderBy('date')
            ->execute();

        return $stmt->fetchAllAssociative();
    }

    public function all(int $page, int $size, string $sort = 'date', string $direction = 'asc'): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                't.id',
                't.user_name as name',
                't.user_email as email',
                't.content',
                't.completed',
                't.edited',
                't.date',
            )
            ->from('tasks', 't');

        if (!in_array($sort, ['user_name', 'user_email', 'content', 'completed'], true)) {
            $sort = 'date';
        }
        if(!in_array($direction, ['ask', 'desc'])){
            $direction = 'asc';
        }

        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }

}