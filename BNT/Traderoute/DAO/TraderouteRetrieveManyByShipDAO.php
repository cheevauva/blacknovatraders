<?php

declare(strict_types=1);

namespace BNT\Traderoute\DAO;

use BNT\Traderoute\Enum\TraderouteTypeEnum;
use BNT\Ship\Entity\Ship;

class TraderouteRetrieveManyByShipDAO extends TraderouteDAO
{
    public ?array $traderoutes = [];
    public Ship $ship;

    public function serve(): void
    {
        $this->traderoutes = [];
        $this->traderoutes += TraderouteRetrieveManyByShipAndSourceTypeDAO::call($this->container, $this->ship, TraderouteTypeEnum::Port);
        $this->traderoutes += TraderouteRetrieveManyByShipAndSourceTypeDAO::call($this->container, $this->ship, TraderouteTypeEnum::Defense);
        $this->traderoutes += TraderouteRetrieveManyByShipAndSourceTypeDAO::call($this->container, $this->ship, TraderouteTypeEnum::Personal);
        $this->traderoutes += TraderouteRetrieveManyByShipAndSourceTypeDAO::call($this->container, $this->ship, TraderouteTypeEnum::Corperate);
    }

    public static function call(\Psr\Container\ContainerInterface $container, Ship $ship): array
    {
        $self = static::new($container);
        $self->ship = $ship;
        $self->serve();

        return $self->traderoutes;
    }
}
