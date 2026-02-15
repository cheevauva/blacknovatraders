<?php

declare(strict_types=1);

namespace BNT;

class UUID
{

    public static function v7(): string
    {
        $timestamp = intval(microtime(true) * 1000);

        return sprintf(...[
            '%02x%02x%02x%02x-%02x%02x-%04x-%04x-%012x',
            ($timestamp >> 40) & 0xFF,
            ($timestamp >> 32) & 0xFF,
            ($timestamp >> 24) & 0xFF,
            ($timestamp >> 16) & 0xFF,
            ($timestamp >> 8) & 0xFF,
            $timestamp & 0xFF,
            mt_rand(0, 0x0FFF) | 0x7000, mt_rand(0, 0x3FFF) | 0x8000, mt_rand(0, 0xFFFFFFFFFFFF)
        ]);
    }
}
