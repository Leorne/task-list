<?php

namespace Console\Commands;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\UseCase\User\Create;

class CreateAdmin extends Command
{
    private const NAME = 'app:create-admin';

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName(self::NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new Create\Command();
        $command->name = 'admin';
        $command->email = 'admin@admin.com';
        $command->password = '123';
        $handler = $this->container->get(Create\Handler::class);

        try {
            $handler->handle($command);
            return Command::SUCCESS;
        } catch (\DomainException $e) {
            $output->write($e->getMessage(), true);
            return Command::FAILURE;
        }
    }
}