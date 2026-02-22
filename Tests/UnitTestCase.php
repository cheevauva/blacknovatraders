<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use UUA\Container\Container;
use BNT\Language;

class UnitTestCase extends TestCase
{

    public static ContainerInterface $container;
    public static Language $l;

    protected function setUp(): void
    {
        self::$container = $this->container();
        self::$l = Language::instance();
    }

    protected function container(): ContainerInterface
    {
        return new Container(fn($c) => $this->stubs($c));
    }

    /**
     * @return array<string, mixed>
     */
    protected function stubs(): array
    {
        return [
        ];
    }
}
