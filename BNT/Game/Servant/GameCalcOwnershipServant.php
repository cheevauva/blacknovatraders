<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use Psr\Container\ContainerInterface;
use BNT\Planet\DAO\PlanetsBaseOwnersBySectorDAO;
use BNT\Sector\DAO\SectorUpdateByCriteriaDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Zone\ZoneConstants;
use BNT\Zone\DAO\ZoneByCriteriaDAO;

class GameCalcOwnershipServant extends \UUA\Servant
{

    public int $sector;

    #[\Override]
    public function serve(): void
    {
        global $min_bases_to_own;

        $owners = PlanetsBaseOwnersBySectorDAO::call($this->container, $this->sector);

        if (empty($owners)) {
            return;
        }

        $owner_num = count($owners);

        $numberOfCorp = 0;

        foreach ($owners as $owner) {
            if ($owner['type'] == 'C') {
                $numberOfCorp++;
            }
        }

        if ($numberOfCorp > 1) {
            $this->sectorAsWarzone();
            return;
        }

        $numberOfShips = 0;
        $ships = [];
        $shipCorps = [];

        foreach ($owners as $owner) {
            if ($owner['type'] == 'C') {
                continue;
            }

            $currentShip = ShipByIdDAO::call($this->container, $owner['id'])->ship;

            if (!$currentShip) {
                continue;
            }

            $ships[] = $currentShip['id'];
            $shipCorps[] = $currentShip['team'];
            $numberOfShips++;
        }

        $numberOfUnallied = 0;

        foreach ($shipCorps as $corp) {
            if ($corp == 0) {
                $numberOfUnallied++;
            }
        }

        if ($numberOfUnallied > 1) {
            $this->sectorAsWarzone();
            return;
        }

        if ($numberOfUnallied == 1 && $numberOfCorp == 1) {
            $this->sectorAsWarzone();
            return;
        }

        if ($numberOfUnallied == 1 && !empty(array_filter($shipCorps))) {
            $this->sectorAsWarzone();
        }


        $winner = [];

        foreach ($owners as $owner) {
            $winner['num'] ??= 0;

            if ($owner['num'] > $winner['num']) {
                $winner = $owner;
                continue;
            }

            if ($owner['num'] == $winner['num'] && $owner['type'] == 'C') {
                $winner = $owner;
            }
        }

        if ($winner['num'] < $min_bases_to_own) {
            $this->sectorAsUncarteredSpaceZone();
            return;
        }

        if ($winner['type'] == 'C') {
            $zone = ZoneByCriteriaDAO::call($this->container, [
                'corp_zone' => 'Y',
                'owner' => $winner['id'],
            ])->zone;

            $this->sectorAsZone((int) $zone['zone_id']);
        } else {
            foreach ($owners as $currentOwner) {
                if ($currentOwner['type'] == 'S' && $currentOwner['id'] != $winner['id'] && $currentOwner['num'] == $winner['num']) {
                    $this->sectorAsUncarteredSpaceZone();
                    return;
                }
            }

            $zone = ZoneByCriteriaDAO::call($this->container, [
                'corp_zone' => 'N',
                'owner' => $winner['id'],
            ])->zone;

            $this->sectorAsZone((int) $zone['zone_id']);
        }
    }

    protected function sectorAsZone(int $zone): void
    {
        SectorUpdateByCriteriaDAO::call($this->container, [
            'zone_id' => $zone,
        ], [
            'sector_id' => $this->sector,
        ]);
    }

    protected function sectorAsUncarteredSpaceZone(): void
    {
        SectorUpdateByCriteriaDAO::call($this->container, [
            'zone_id' => ZoneConstants::ZONE_ID_UNCHARTERED_SPACE,
        ], [
            'sector_id' => $this->sector,
        ]);
    }

    protected function sectorAsWarzone(): void
    {
        SectorUpdateByCriteriaDAO::call($this->container, [
            'zone_id' => ZoneConstants::ZONE_ID_WAR_ZONE,
        ], [
            'sector_id' => $this->sector,
        ]);
    }

    public static function call(ContainerInterface $container, int $sector): self
    {
        $self = self::new($container);
        $self->sector = $sector;
        $self->serve();

        return $self;
    }
}
