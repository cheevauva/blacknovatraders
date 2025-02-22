<?php

declare(strict_types=1);

namespace BNT\Ship\ShipRanking\DAO;

abstract class ShipRankingTopCachedDAO implements \BNT\DAO
{
    use \BNT\Traits\CacheTrait;

    protected ShipRankingTopDAO $shipRankingTop;
    public array $ships;

    abstract protected function cacheKey(): string;

    protected function cacheExpires(): int
    {
        return 3600;
    }

    public function serve(): void
    {
        $item = $this->cache()->getItem($this->cacheKey());

        if (!$item->isHit()) {
            $this->shipRankingTop->serve();

            $this->ships = $this->shipRankingTop->ships;

            $item->set(serialize($this->ships));
            $item->expiresAfter($this->cacheExpires());

            $this->cache()->save($item);
        } else {
            $this->ships = unserialize($item->get());
        }
    }

    public static function call(\Psr\Container\ContainerInterface $container, ): array
    {
        $self = static::new($container);
        $self->serve();

        return $self->ships;
    }
}
