<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Application;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Tools\Console\Command;
use Symfony\Component\Dotenv\Dotenv;
use Console\EmLoader;

chdir(dirname(__DIR__));
$loader = require 'vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

if (file_exists('.env')) {
    (new Dotenv(true))->load('.env');
}



$container = require 'config/container.php';

$entityManager = $container->get(EntityManagerInterface::class);

$connection = $entityManager->getConnection();

$configuration = new Configuration();

$configuration->addMigrationsDirectory('MyProject\Migrations', __DIR__ . '/../migrations');
$configuration->setAllOrNothing(true);
$configuration->setCheckDatabasePlatform(false);

$storageConfiguration = new TableMetadataStorageConfiguration();
$storageConfiguration->setTableName('doctrine_migration_versions');

$configuration->setMetadataStorageConfiguration($storageConfiguration);

$dependencyFactory = DependencyFactory::fromEntityManager(
    new ExistingConfiguration($configuration),
    new EmLoader($entityManager)
);

$cli = new Application('Doctrine Migrations');
$cli->setCatchExceptions(true);

$cli->addCommands([
    new Command\DiffCommand($dependencyFactory),
    new Command\DumpSchemaCommand($dependencyFactory),
    new Command\ExecuteCommand($dependencyFactory),
    new Command\GenerateCommand($dependencyFactory),
    new Command\LatestCommand($dependencyFactory),
    new Command\ListCommand($dependencyFactory),
    new Command\MigrateCommand($dependencyFactory),
    new Command\RollupCommand($dependencyFactory),
    new Command\StatusCommand($dependencyFactory),
]);

$cli->addCommands([
        $container->get(Console\Commands\CreateAdmin::class)
    ]);

$cli->run();