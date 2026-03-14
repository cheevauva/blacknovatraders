<?php

declare(strict_types=1);

namespace BNT\Traits;

use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Ship\DAO\ShipByIdDAO;

trait PlayerinfoTrait
{

    public array $playerinfo;

    protected function playerinfoTurn(int $turns = 1): void
    {
        $this->playerinfo['turns'] -= $turns;
        $this->playerinfo['turns_used'] += $turns;
    }

    protected function playerinfoReload(): void
    {
        $this->playerinfo = ShipByIdDAO::call($this->container, $this->playerinfo['ship_id'])->ship;
    }

    protected function playerinfoUpdate(): void
    {
        ShipUpdateDAO::call($this->container, $this->playerinfo, $this->playerinfo['ship_id']);
    }
}
