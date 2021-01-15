<?php

use App\Controller\Auth\LoginController;
use App\Controller\Task\TaskController;
use App\Middleware\AuthMiddleware;

/**
 * @var \League\Route\Router $router
 * @var \Psr\Container\ContainerInterface $container
 */

$router->map('GET','/', [TaskController::class, 'index'])->setName('tasks');

$router->map('GET','/create', [TaskController::class, 'create'])->setName('tasks.create');
$router->map('POST','/create', [TaskController::class, 'create']);

$router->map('GET','/{id}/edit', [TaskController::class, 'edit'])
    ->setName('tasks.edit')
    ->middleware($container->get(AuthMiddleware::class));

$router->map('POST','/{id}/edit', [TaskController::class, 'edit'])
    ->middleware($container->get(AuthMiddleware::class));;

$router->map('POST','/{id}/complete', [TaskController::class, 'complete'])
    ->setName('tasks.complete')
    ->middleware($container->get(AuthMiddleware::class));

$router->map('GET','/login', [LoginController::class, 'login'])->setName('login');
$router->map('POST','/login', [LoginController::class, 'login']);

$router->map('GET','/logout', [LoginController::class, 'logout'])
    ->setName('logout')
    ->middleware($container->get(AuthMiddleware::class));