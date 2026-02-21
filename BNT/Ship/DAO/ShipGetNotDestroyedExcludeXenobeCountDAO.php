<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use Psr\Container\ContainerInterface;

class ShipGetNotDestroyedExcludeXenobeCountDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $count;

    #[\Override]
    public function serve(): void
    {
        $this->count = (int) $this->db()->column("SELECT COUNT(*) AS num_players FROM ships WHERE ship_destroyed='N'");
    }

    public static function call(ContainerInterface $container): self
    {
        $self = self::new($container);
        $self->serve();

        return $self;
    }
}
