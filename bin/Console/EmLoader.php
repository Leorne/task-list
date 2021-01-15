<?php

namespace Console;

use Doctrine\Migrations\Configuration\EntityManager\EntityManagerLoader;
use Doctrine\ORM\EntityManagerInterface;

class EmLoader implements EntityManagerLoader
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }
}