<?php

declare(strict_types=1);

namespace BNT\Traits;

use BNT\Language;

trait LanguageTrait
{

    protected function l(): Language
    {
        return Language::instance();
    }
}
