<?php

namespace Infrastructure\Template\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Webmozart\Assert\Assert;

class AssetsExtension extends AbstractExtension
{
    private string $appUrl;

    public function __construct(string $appUrl)
    {
        Assert::notEmpty($appUrl);
        $this->appUrl = $appUrl;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('assets', [$this, 'generatePath']),
        ];
    }

    public function generatePath(string $path): string
    {
        Assert::notEmpty($path);
        return $this->appUrl . '/' . trim($path);
    }
}