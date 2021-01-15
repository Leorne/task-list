<?php

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\DBAL;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;
use App\Entity;

return [
    'dependencies' => [
        'factories' => [
            EntityManagerInterface::class => static function (ContainerInterface $container) {
                $params = $container->get('config')['doctrine'];
                $config = Setup::createAnnotationMetadataConfiguration(
                    $params['metadata_dirs'],
                    $params['dev_mode'],
                    $params['cache_dir'],
                    new FilesystemCache(
                        $params['cache_dir']
                    ),
                    false
                );
                foreach ($params['types'] as $type => $class) {
                    if (!DBAL\Types\Type::hasType($type)) {
                        DBAL\Types\Type::addType($type, $class);
                    }
                }
                return EntityManager::create(
                    $params['connection'],
                    $config
                );
            },
            Connection::class => static function (ContainerInterface $container) {
                $params = $container->get('config')['doctrine'];

                $config = Setup::createAnnotationMetadataConfiguration(
                    $params['metadata_dirs'],
                    $params['dev_mode'],
                    $params['cache_dir'],
                    new FilesystemCache(
                        $params['cache_dir']
                    ),
                    false
                );

                return DriverManager::getConnection(
                    $params['connection'],
                    $config
                );
            }
        ]
    ],

    'doctrine' => [
        'dev_mode' => false,
        'cache_dir' => 'var/cache/doctrine',
        'metadata_dirs' => [
            'src/App/Entity',
        ],
        'connection' => [
            'url' => getenv('DATABASE_URL'),
        ],
        'types' => [
            Entity\Task\IdType::NAME => Entity\Task\IdType::class,
            Entity\User\IdType::NAME => Entity\User\IdType::class,
        ],
    ],
];