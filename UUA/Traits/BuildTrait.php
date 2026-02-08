<?php

//declare(strict_types=1);

namespace UUA\Traits;

trait BuildTrait
{

    public static function _new($container)
    {
        return new static($container);
    }
}
