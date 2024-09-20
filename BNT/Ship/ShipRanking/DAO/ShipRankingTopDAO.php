<?php

declare(strict_types=1);

namespace BNT\Ship\ShipRanking\DAO;

use BNT\Ship\DAO\ShipDAO;
use BNT\Traits\CacheTrait;

abstract class ShipRankingTopDAO extends ShipDAO
{
    use CacheTrait;

    public array $ships = [];

    abstract protected function cacheKey(): string;

    protected function cacheExpires(): int
    {
        return 3600;
    }

    abstract protected function getShips(): array;

    public function serve(): void
    {
        $item = $this->cache()->getItem($this->cacheKey());

        if (!$item->isHit()) {
            $ships = $this->getShips();
            $item->set($ships);
            $item->expiresAfter($this->cacheExpires());
            $this->cache()->save($item);
        } else {
            $ships = $item->get();
        }

        $this->ships = $this->asShips($ships);
    }

    public static function call(): array
    {
        $self = new static;
        $self->serve();

        return $self->ships;
    }
}
