<?php

declare(strict_types=1);

namespace BNT\Ship\ShipRanking\DAO;

use BNT\Ship\DAO\ShipDAO;

abstract class ShipRankingTopDAO extends ShipDAO
{
    public array $ships = [];

    public function serve(): void
    {
        $this->ships = $this->asShips($this->ships());
    }

    abstract protected function ships(): array;

    public static function call(): array
    {
        $self = new static;
        $self->serve();

        return $self->ships;
    }
}
