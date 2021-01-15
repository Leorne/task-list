<?php

namespace Infrastructure\Template\Twig\Extensions;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('status', [$this, 'status'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function status(Environment $twig, bool $status, string $type): string
    {
        return $twig->render('widget/task/status.html.twig', [
            'status' => $status,
            'type' => $type,
        ]);
    }
}