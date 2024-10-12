<?php

declare(strict_types=1);

namespace BNT\Traits;

trait BuildTrait
{
    public static function build(): static
    {
        return new static;
    }
}
