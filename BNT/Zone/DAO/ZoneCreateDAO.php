<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

use Psr\Container\ContainerInterface;

class ZoneCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    public array $zone;
    public int $id;

    #[\Override]
    public function serve(): void
    {
        $this->id = $this->zone['zone_id'] = $this->rowCreate('zones', $this->zone);
    }

    public static function call(ContainerInterface $container, array $ship): self
    {
        $self = self::new($container);
        $self->zone = $ship;
        $self->serve();

        return $self;
    }
}
