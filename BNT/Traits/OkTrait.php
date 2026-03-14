<?php

declare(strict_types=1);

namespace BNT\Traits;

trait OkTrait
{

    public protected(set) bool $ok = true;

    public function isOk(): bool
    {
        return $this->ok;
    }

    public function isNotOk(): bool
    {
        return !$this->ok;
    }

    public function ok(): void
    {
        $this->ok = true;
    }

    public function notOk(): void
    {
        $this->ok = false;
    }

    public function passOk(bool $ok): void
    {
        $this->ok = $ok;
    }
}
