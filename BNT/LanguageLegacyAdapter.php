<?php

declare(strict_types=1);

namespace BNT;

use BNT\Language;

class LanguageLegacyAdapter
{

    protected Language $language;

    public function __construct(string $language)
    {
        $this->language = new Language($language);
    }

    public function __get(string $name): mixed
    {
        if (isset($GLOBALS[$name])) {
            return $GLOBALS[$name];
        }

        $GLOBALS[$name] ??= $this->language->{$name};

        if (isset($GLOBALS[$name])) {
            return $GLOBALS[$name];
        }

        return $name;
    }
}
