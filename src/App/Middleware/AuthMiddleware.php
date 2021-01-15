<?php

namespace App\Middleware;

use Infrastructure\Security\SecurityInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    private SecurityInterface $security;

    public function __construct(SecurityInterface $security)
    {
        $this->security = $security;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->security->isAuth()) {
            return $handler->handle($request);
        }

        return new RedirectResponse('/', 401);
    }
}