<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\Cache\CacheItemPoolInterface;
use BNT\Cache;

trait CacheTrait
{

    protected function cache(): CacheItemPoolInterface
    {
        return Cache::instance();
    }

}
