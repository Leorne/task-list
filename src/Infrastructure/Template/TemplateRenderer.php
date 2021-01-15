<?php

namespace Infrastructure\Template;

interface TemplateRenderer
{
    public function render(string $name, array $params): string;
}