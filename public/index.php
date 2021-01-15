<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router;
use Symfony\Component\Dotenv\Dotenv;

chdir(dirname(__DIR__));

$loader = require 'vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

if (file_exists('.env')) {
    (new Dotenv(true))->load('.env');
}


$container = require 'config/container.php';

/** @var \League\Route\Router $router */

$router = $container->get(Router::class);
$strategy = (new League\Route\Strategy\ApplicationStrategy)->setContainer($container);
$router->setStrategy($strategy);

require 'config/routes.php';

$request = ServerRequestFactory::fromGlobals();
$response = $router->dispatch($request);

$emitter = new SapiEmitter();
$emitter->emit($response);