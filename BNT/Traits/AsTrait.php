<?php

declare(strict_types=1);

namespace BNT\Traits;

trait AsTrait
{

    public static function as(self $self): static
    {
        return $self;
    }
}
