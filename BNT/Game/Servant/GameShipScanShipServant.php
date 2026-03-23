<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Ship\Ship;

class GameShipScanShipServant extends \UUA\Servant
{

    public int $base = 5;
    public int $multiply = 5;
    public Ship $player;
    public Ship $target;
    public protected(set) bool $isSuccess = false;

    #[\Override]
    public function serve(): void
    {
        $success = ($this->base - $this->target->cloak + $this->player->sensors) * $this->multiply;

        if ($success < 5) {
            $success = 5;
        }
        if ($success > 95) {
            $success = 95;
        }

        $this->isSuccess = $this->roll() > $success;
    }

    protected function roll(): int
    {
        return  rand(1, 100);
    }
}
