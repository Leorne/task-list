<?php

use Console\Commands\CreateAdmin;
use Psr\Container\ContainerInterface;

return [
    'dependencies' => [
        'factories' => [
            CreateAdmin::class => static function (ContainerInterface $container) {
                return new CreateAdmin(
                    $container
                );
            }
        ],
    ],
];