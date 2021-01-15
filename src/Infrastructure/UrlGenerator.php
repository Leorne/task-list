<?php

namespace Infrastructure;

use League\Route\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class UrlGenerator implements UrlGeneratorInterface
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function generate(?string $name = null, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH) : string
    {
        if(!$name){
            $route = $this->router->getNamedRoute('tasks');
            $path = $route->getPath();
            $path .= '?' . http_build_query($parameters);
            return $path;
        }
        $route = $this->router->getNamedRoute($name);
        return $route->getPath($parameters);
    }

    public function setContext(RequestContext $context)
    {
        // TODO: Implement setContext() method.
    }

    public function getContext()
    {
        // TODO: Implement getContext() method.
    }
}