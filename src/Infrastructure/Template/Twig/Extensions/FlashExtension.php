<?php

namespace Infrastructure\Template\Twig\Extensions;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlashExtension extends AbstractExtension
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('flashes', [$this, 'getFlash']),
        ];
    }

    public function getFlash(string $type) : array
    {
        return $this->session->getFlashBag()->get($type, []);
    }
}