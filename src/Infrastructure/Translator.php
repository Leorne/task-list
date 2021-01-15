<?php

namespace Infrastructure;

use Symfony\Contracts\Translation\TranslatorInterface;

class Translator implements TranslatorInterface
{

    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null)
    {
        return '';
    }
}