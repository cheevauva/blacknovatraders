<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use UUA\Container\Container;

class UnitTestCase extends TestCase
{

    public static ContainerInterface $container;

    protected function setUp(): void
    {
        self::$container = $this->container();
    }

    protected function container(): ContainerInterface
    {
        return new \UUA\Container\Container(fn($c) => $this->stubs($c));
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
