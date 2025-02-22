<?php

declare(strict_types=1);

namespace BNT\Planet\Servant;

use BNT\Servant;
use BNT\Sector\Entity\Sector;
use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\Sector\Servant\SectorCalcOwnershipServant;
use BNT\Planet\DAO\PlanetRetrieveByIdDAO;
use BNT\Planet\DAO\PlanetSaveDAO;
use BNT\Planet\Entity\Planet;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Ship\Servant\ShipRealSpaceMoveServant;
use BNT\Enum\BalanceEnum;


class PlanetBuildBaseServant extends Servant
{

    
    public Ship $ship;
    public Sector $sector;
    public Planet $planet;
    public int $sector_id;
    public int $planet_id;
    public bool $doIt = true;
    public ShipRealSpaceMoveServant $realSpaceMove;
    public ?SectorCalcOwnershipServant $calcOwnership = null;

    #[\Override]
    public function serve(): void
    {
        $this->sector = SectorRetrieveByIdDAO::call($this->container, $this->sector_id);
        $this->planet = PlanetRetrieveByIdDAO::call($this->container, $this->planet_id);

        $realSpaceMove = $this->realSpaceMove = ShipRealSpaceMoveServant::new($this->container);
        $realSpaceMove->ship = $this->ship;
        $realSpaceMove->destination = $this->sector_id;
        $realSpaceMove->doIt = $this->doIt;
        $realSpaceMove->serve();

        if ($this->isCanBuildBase()) {
            $this->planet->base = true;
            $this->planet->ore -= BalanceEnum::base_ore->val();
            $this->planet->organics -= BalanceEnum::base_organics->val();
            $this->planet->goods -= BalanceEnum::base_goods->val();
            $this->planet->credits -= BalanceEnum::base_credits->val();
        }

        $this->doIt();
    }

    private function isCanBuildBase(): bool
    {
        $youCanBuildBase = true;
        $youCanBuildBase &= !$this->planet->base;
        $youCanBuildBase &= $this->planet->ore >= BalanceEnum::base_ore->val();
        $youCanBuildBase &= $this->planet->organics >= BalanceEnum::base_organics->val();
        $youCanBuildBase &= $this->planet->goods >= BalanceEnum::base_goods->val();
        $youCanBuildBase &= $this->planet->credits >= BalanceEnum::base_credits->val();

        return !empty($youCanBuildBase);
    }

    private function doIt(): void
    {
        if (!$this->doIt) {
            return;
        }

        $this->ship->turn();

        ShipSaveDAO::call($this->container, $this->ship);
        PlanetSaveDAO::call($this->container, $this->planet);

        $this->calcOwnership = SectorCalcOwnershipServant::call($this->container, $this->ship->sector);
    }
}
