<?php

declare(strict_types=1);

namespace BNT;

use Psr\Cache\CacheItemPoolInterface;
use BNT\Cache;

trait CacheTrait
{

    protected function cache(): CacheItemPoolInterface
    {
        return Cache::instance();
    }

}
