<?php

namespace App\UseCase\User\Create;

use App\Entity\User\Id;
use App\Entity\User\User;
use App\Entity\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Services\Flusher;
use Infrastructure\Services\User\PasswordHasherInterface;

class Handler
{
    private EntityManagerInterface $em;
    private Flusher $flusher;
    private PasswordHasherInterface $hasher;
    private UserRepository $users;

    public function __construct(UserRepository $users, EntityManagerInterface $em, Flusher $flusher, PasswordHasherInterface $hasher)
    {
        $this->em = $em;
        $this->flusher = $flusher;
        $this->hasher = $hasher;
        $this->users = $users;
    }

    public function handle(Command $command): void
    {
        if($this->users->hasByName($command->name)){
            throw new \DomainException('User with such name already exists.');
        }

        $user = new User(
            Id::next(),
            $command->name,
            $command->email,
            $this->hasher->hash($command->password)
        );

        $this->em->persist($user);
        $this->flusher->flush();
    }
}