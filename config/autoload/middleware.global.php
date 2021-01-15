<?php

use App\Middleware\AuthMiddleware;
use Infrastructure\Security\SecurityInterface;
use Psr\Container\ContainerInterface;

return [
    'dependencies' => [
        'factories' => [
            AuthMiddleware::class => static function (ContainerInterface $container) {
                return new AuthMiddleware($container->get(SecurityInterface::class));
            },
        ],
    ]
];