<?php

declare(strict_types=1);

namespace BNT\Traits;

use BNT\Translate;

trait TranslateTrait
{

    protected function t(array|string $tag, array $replace = []): Translate
    {
        return new Translate()->translate($tag, $replace);
    }
}
