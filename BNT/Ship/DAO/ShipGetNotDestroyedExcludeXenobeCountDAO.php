<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use Psr\Container\ContainerInterface;

class ShipGetNotDestroyedExcludeXenobeCountDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public protected(set) int $count;

    #[\Override]
    public function serve(): void
    {
        $this->count = $this->db()->column("SELECT COUNT(*) AS num_players FROM ships WHERE ship_destroyed='N' and email NOT LIKE '%@xenobe'");
    }

    public static function call(ContainerInterface $container): self
    {
        $self = self::new($container);
        $self->serve();

        return $self;
    }
}
