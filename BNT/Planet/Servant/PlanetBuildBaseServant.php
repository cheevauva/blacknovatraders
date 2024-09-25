<?php

declare(strict_types=1);

namespace BNT\Planet\Servant;

use BNT\ServantInterface;
use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\Sector\Entity\Sector;
use BNT\Planet\DAO\PlanetRetrieveByIdDAO;
use BNT\Planet\DAO\PlanetSaveDAO;
use BNT\Planet\Entity\Planet;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Enum\BalanceEnum;
use BNT\Servant\RealSpaceMoveServant;
use BNT\Planet\DTO\CalcOwnershipDTO;

class PlanetBuildBaseServant implements ServantInterface
{

    public Ship $ship;
    public Sector $sector;
    public Planet $planet;
    public int $sector_id;
    public int $planet_id;
    public bool $doIt = true;
    public RealSpaceMoveServant $realSpaceMove;

    #[\Override]
    public function serve(): void
    {
        $this->sector = SectorRetrieveByIdDAO::call($this->sector_id);
        $this->planet = PlanetRetrieveByIdDAO::call($this->planet_id);

        if ($this->isCanBuildBase()) {
            $this->planet->base = true;
            $this->planet->ore -= BalanceEnum::base_ore->val();
            $this->planet->organics -= BalanceEnum::base_organics->val();
            $this->planet->goods -= BalanceEnum::base_goods->val();
            $this->planet->credits -= BalanceEnum::base_credits->val();
        }

        $this->realSpaceMove = new RealSpaceMoveServant;
        $this->realSpaceMove->destination = $this->sector_id;
        $this->realSpaceMove->doIt = $this->doIt;
        $this->realSpaceMove->serve();

        $this->doIt();
    }

    private function isCanBuildBase(): bool
    {
        $youCanBuildBase = true;
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

        ShipSaveDAO::call($this->ship);
        PlanetSaveDAO::call($this->planet);
    }

}
