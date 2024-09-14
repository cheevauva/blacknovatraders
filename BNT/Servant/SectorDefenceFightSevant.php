<?php

declare(strict_types=1);

namespace BNT\Servant;

use BNT\ServantInterface;
use BNT\SectorDefence\SectorDefence;
use BNT\Log\LogDefenceKaboom;
use BNT\Log\LogDefenceDestroyedFighters;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveTotalFightersBySectorIdDAO;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Log\DAO\LogCreateDAO;
use BNT\BalanceEnum;
use BNT\SectorDefence\Servant\SectorDefenceDestroyFightersServant;
use BNT\Message\Servant\MessageDefenceOwnerServant;
use BNT\Ship\Servant\ShipDestroyServant;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;

class SectorDefenceFightSevant implements ServantInterface
{

    public bool $doIt = true;
    public Ship $ship;
    public int $sector_id;
    //
    private int $playerArmor;
    private int $playerBeams;
    private int $playerShields;
    private int $playerTorps;
    private int $playerTorpDmg;
    private int $playerFighters;
    private int $targetFighters;
    public int $totalSectorFighters;
    public int $fightersLost = 0;
    public int $playerTorpNum = 0;
    public int $armorLost = 0;
    public bool $shipDestroyed = false;
    public bool $hasEscapePod = false;
    public int $yourBeamsDestroyeFighters = 0;
    public int $yourTorpedoesDestroyedFighters = 0;
    public int $yourFightersDestroyedFighters = 0;
    public int $youLostFighters = 0;
    public int $yourArmorbreach = 0;
    public SectorDefenceDestroyFightersServant $destroyFighters;
    public ?ShipDestroyServant $destroyShip = null;
    public array $logs = [];

    public function serve(): void
    {
        global $energyscooped;
        global $l_sf_sendlog;
        global $torp_dmg_rate;
        
        
        $hasMyDefence = new SectorDefenceRetrieveManyByCriteriaDAO;
        $hasMyDefence->sector_id = $this->sector_id;
        $hasMyDefence->ship_id = $this->ship->ship_id;
        $hasMyDefence->limit = 1;
        $hasMyDefence->serve();

        if ($hasMyDefence->defences) {
            return;
        }

        $this->totalSectorFighters = SectorDefenceRetrieveTotalFightersBySectorIdDAO::call($this->sector_id);

//        if ($this->calledFrom === static::CALLED_FROM_RSMOVE) {
//            $this->ship->ship_energy += $energyscooped;
//        }

        $this->targetFighters = $this->totalSectorFighters;
        $this->playerBeams = $this->calculatePlayerBeams();
        $this->playerShields = $this->calculatePlayerShields();
        $this->playerTorps = $this->calculatePlayerTorps();
        $this->playerTorpDmg = $torp_dmg_rate * $this->playerTorps;
        $this->playerArmor = $this->ship->armor_pts;
        $this->playerFighters = $this->ship->ship_fighters;
        //
        $this->ship->ship_energy = $this->ship->ship_energy - $this->playerBeams;

        $this->beamsVersusFighters();
        $this->torpsVersusFighters();
        $this->fightersVersusFighters();
        $this->armorVersusFighters();

        $fighterslost = $this->totalSectorFighters - $this->targetFighters;

        $this->destroyFighters($fighterslost);
        $this->messageDefenceOwner($this->sector_id, strtr($l_sf_sendlog, [
            '[player]' => $this->ship->character_name,
            '[lost]' => $fighterslost,
            '[sector]' => $this->sector_id,
        ]));

        $this->logDefenceDestroyedFighters($fighterslost);

        $armorLost = $this->ship->armor_pts - $this->playerArmor;
        $fightersLost = $this->ship->ship_fighters - $this->playerFighters;

        $this->ship->ship_fighters = $this->ship->ship_fighters - $fightersLost;
        $this->ship->armor_pts = $this->ship->armor_pts - $armorLost;
        $this->ship->torps = $this->ship->torps - $this->playerTorps;

        $this->armorLost = $armorLost;
        $this->fightersLost = $fightersLost;
        $this->playerTorpNum = $this->playerTorps;

        if ($this->playerArmor < 1) {
            $this->destroy();
        }

        $this->doIt();
    }

    private function doIt(): void
    {
        if (!$this->doIt) {
            return;
        }

        ShipSaveDAO::call($this->ship);

        foreach ($this->logs as $log) {
            LogCreateDAO::call($log);
        }
    }

    private function logDefenceDestroyedFighters(int $fighterslost): void
    {
        if ($fighterslost < 1) {
            return;
        }
        
        $log = new LogDefenceDestroyedFighters;
        $log->ship_id = $this->ship->ship_id;
        $log->fighterslost = $fighterslost;
        $log->sector = $this->sector_id;

        $this->logs[] = $log;
    }

    private function logKaboom(): void
    {
        $log = new LogDefenceKaboom;
        $log->sector = $this->sector_id;
        $log->dev_escapepod = $this->ship->dev_escapepod;

        $this->logs[] = $log;
    }

    protected function destroyFighters(int $fighterslost): void
    {
        $this->destroyFighters = new SectorDefenceDestroyFightersServant;
        $this->destroyFighters->sector = $this->sector_id;
        $this->destroyFighters->fighters = $fighterslost;
        $this->destroyFighters->doIt = $this->doIt;
        $this->destroyFighters->serve();
    }

    private function calculatePlayerTorps(): int
    {
        $torp = round(mypw(BalanceEnum::level_factor->val(), $this->ship->torp_launchers)) * 2;

        if ($torp > $this->ship->torps) {
            $torp = $this->ship->torps;
        }

        return $torp;
    }

    private function calculatePlayerShields(): int
    {
        $shields = NUM_SHIELDS($this->ship->shields);

        if ($shields > $this->ship->shields) {
            $shields = $this->ship->shields;
        }

        return $shields;
    }

    private function calculatePlayerBeams(): int
    {
        $beams = intval(NUM_BEAMS($this->ship->beams));

        if ($beams > $this->ship->ship_energy) {
            $beams = $this->ship->ship_energy;
        }

        return $beams;
    }

    private function beamsVersusFighters(): void
    {
        if ($this->targetFighters < 1 || $this->playerBeams < 1) {
            return;
        }

        if ($this->playerBeams > intval(round($this->targetFighters / 2))) {
            $temp = intval(round($this->targetFighters / 2));
            $lost = $this->targetFighters - $temp;
            $this->targetFighters = $temp;
            $this->playerBeams -= $lost;
            $this->yourBeamsDestroyeFighters = $lost;
        } else {
            $this->targetFighters -= $this->playerBeams;
            $this->yourBeamsDestroyeFighters = $this->playerBeams;
            $this->playerBeams = 0;
        }
    }

    private function torpsVersusFighters(): void
    {
        if ($this->targetFighters < 1 || $this->playerTorpDmg < 1) {
            return;
        }

        if ($this->playerTorpDmg > round($this->targetFighters / 2)) {
            $temp = round($this->targetFighters / 2);
            $lost = $this->targetFighters - $temp;
            $this->targetFighters = $temp;
            $this->playerTorpDmg -= $lost;
            $this->yourTorpedoesDestroyedFighters = $lost;
        } else {
            $this->targetFighters -= $this->playerTorpDmg;
            $this->yourTorpedoesDestroyedFighters = $this->playerTorpDmg;
            $this->playerTorpDmg = 0;
        }
    }

    private function fightersVersusFighters(): void
    {
        if ($this->playerFighters < 1 || $this->targetFighters < 1) {
            return;
        }

        if ($this->playerFighters > $this->targetFighters) {
            $this->yourFightersDestroyedFighters = $this->targetFighters;
            $this->targetFighters = 0;
        } else {
            $this->yourFightersDestroyedFighters = $this->playerFighters;
            $this->targetFighters -= $this->playerFighters;
        }

        if ($this->targetFighters > $this->playerFighters) {
            $this->youLostFighters = $this->playerFighters;
            $this->playerFighters = 0;
        } else {
            $this->youLostFighters = $this->targetFighters;
            $this->playerFighters -= $this->targetFighters;
        }
    }

    private function armorVersusFighters(): void
    {
        if ($this->targetFighters < 1) {
            return;
        }

        if ($this->targetFighters > $this->playerArmor) {
            $this->playerArmor = 0;
            $this->yourArmorbreach = $this->playerArmor;
        } else {
            $this->playerArmor -= $this->targetFighters;
            $this->yourArmorbreach = $this->targetFighters;
        }
    }

    private function messageDefenceOwner(int $sector, string $message): void
    {
        $messageDefenceOwner = new MessageDefenceOwnerServant;
        $messageDefenceOwner->sector = $sector;
        $messageDefenceOwner->message = $message;
        $messageDefenceOwner->doIt = false;
        $messageDefenceOwner->serve();

        array_push($this->logs, ...$messageDefenceOwner->logs);
    }

    private function destroy(): void
    {
        global $l_sf_sendlog2;

        $this->shipDestroyed = true;
        $this->hasEscapePod = $this->ship->dev_escapepod;

        $this->logKaboom();
        $this->messageDefenceOwner($this->sector_id, strtr($l_sf_sendlog2, [
            '[player]' => $this->ship->character_name,
            '[sector]' => $this->sector_id,
        ]));

        $this->destroyShip = new ShipDestroyServant;
        $this->destroyShip->ship = $this->ship;
        $this->destroyShip->doIt = $this->doIt;
        $this->destroyShip->serve();
    }

}
