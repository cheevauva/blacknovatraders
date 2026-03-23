<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Ship\Ship;

class GameShipEscapedFromShipServant extends \UUA\Servant
{

    public int $base = 5;
    public int $multiply = 5;
    public Ship $player;
    public Ship $target;
    public protected(set) bool $isSuccess = false;

    #[\Override]
    public function serve(): void
    {
        $this->isSuccess = ($this->base - $this->target->engines + $this->target->engines) * $this->multiply < $this->roll2();
    }

    protected function roll2(): int
    {
        return rand(1, 100);
    }
}
