<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use Psr\Container\ContainerInterface;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Bounty\DAO\BountyDeleteByCriteriaDAO;
use BNT\SectorDefence\DAO\SectorDefencesDeleteByCriteriaDAO;
use BNT\Sector\DAO\SectorUpdateByCriteriaDAO;
use BNT\Zone\DAO\ZonesByCriteriaDAO;
use BNT\Zone\ZoneConstants;
use BNT\News\DAO\NewsCreateDAO;
use BNT\Planet\DAO\PlanetsByCriteriaDAO;
use BNT\Planet\DAO\PlanetsUpdateByCriteriaDAO;
use BNT\Game\Servant\GameCalcOwnershipServant;

class GameKillPlayerServant extends \UUA\Servant
{

    public int $ship;

    #[\Override]
    public function serve(): void
    {
        global $l;

        ShipUpdateDAO::call($this->container, [
            'ship_destroyed' => 'Y',
            'on_planet' => 'N',
            'cleared_defences' => null,
        ], $this->ship);

        BountyDeleteByCriteriaDAO::call($this->container, [
            'placed_by' => $this->ship
        ]);

        $planets = PlanetsByCriteriaDAO::call($this->container, [
            'owner' => $this->ship,
            'base' => 'Y'
        ])->planets;

        $sectors = array_filter(array_unique(array_column($planets, 'sector_id')));

        PlanetsUpdateByCriteriaDAO::call($this->container, [
            'owner' => 0,
            'fighters' => 0,
            'base' => 'N',
        ], [
            'owner' => $this->ship,
        ]);

        foreach ($sectors as $sector) {
            GameCalcOwnershipServant::call($this->container, $sector);
        }

        SectorDefencesDeleteByCriteriaDAO::call($this->container, [
            'ship_id' => $this->ship,
        ]);

        $zones = ZonesByCriteriaDAO::call($this->container, [
            'corp' => 'N',
            'owner' => $this->ship,
        ])->zones;

        foreach ($zones as $zone) {
            SectorUpdateByCriteriaDAO::call($this->container, [
                'zone_id' => ZoneConstants::ZONE_ID_UNCHARTERED_SPACE,
            ], [
                'zone_id' => $zone['zone_id']
            ]);
        }

        $ship = ShipByIdDAO::call($this->container, $this->ship)->ship;

        NewsCreateDAO::call($this->container, [
            'headline' => $ship['ship_name'] . $l->killheadline,
            'newstext' => str_replace('[name]', $ship['ship_name'], $l->news_killed),
            'ship_id' => $this->ship,
        ]);
    }

    public static function call(ContainerInterface $container, int $ship): self
    {
        $self = self::new($container);
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
