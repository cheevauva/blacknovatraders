<?php

declare(strict_types=1);

namespace BNT;

use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Psr\Cache\CacheItemPoolInterface;

class Cache
{

    private static CacheItemPoolInterface $cache;

    public static function instance(): CacheItemPoolInterface
    {
        if (empty(self::$cache)) {
            self::$cache = new MemcachedAdapter(MemcachedAdapter::createConnection('memcached://memcached'));
        }

        return self::$cache;
    }

}
