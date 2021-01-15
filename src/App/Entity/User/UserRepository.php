<?php

namespace App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class UserRepository
{
    private EntityManagerInterface $em;
    private ObjectRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $this->em->getRepository(User::class);
    }

    public function findByEmail(string $email): ?User
    {
        /**
         * @var $user ?User
         */
        $user = $this->repo->findOneBy(['email' => $email]);
        return $user;
    }

    public function findByName(string $name): ?User
    {
        /**
         * @var $user ?User
         */
        $user = $this->repo->findOneBy(['name' => $name]);
        return $user;
    }

    public function hasByName(string $name): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT (t.id)')
                ->andWhere('t.name = :name')
                ->setParameter('name', $name)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function hasByEmail(string $email): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT (t.id)')
                ->andWhere('t.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

}