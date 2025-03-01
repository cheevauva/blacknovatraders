<?php

declare(strict_types=1);

namespace BNT\Math\Servant;

use BNT\Servant;
use BNT\Enum\BalanceEnum;
use BNT\Math\DTO\MathDefencesDTO;
use BNT\Math\DTO\MathDefenceDTO;
use BNT\Math\DTO\MathShipDTO;

class MathDefenceCalculateMinesServant extends Servant
{

    public MathDefencesDTO $defences;
    public MathShipDTO $ship;
    //
    public int $minesToll = 0;
    public int $totalMines = 0;
    public bool $hasEmenyMines = false;
    public bool $minesAttack = false;
    //
    public bool $lostAllMineDeflectors = false;
    public bool $hadNoMineDeflectors = false;
    public int $lostMineDeflectors = 0;
    public int $lostShipEnergy = 0;
    public bool $shieldsAreDown = false;
    public int $lostArmorPts = 0;
    public bool $hullIsBreached = false;
    public bool $shipHasBeenDestroyed = false;
    //
    protected ?MathDefencesDTO $firstDefence = null;
    protected int $minesLeft = 0;

    public function serve(): void
    {
        $this->firstDefence = null;
        $this->totalMines = 0;

        foreach ($this->defences as $defence) {
            $this->firstDefence ??= $defence;

            $defence = MathDefenceDTO::as($defence);

            if (!$this->hasEmenyMines && $defence->isMines && (!$defence->isOwner || !$defence->isOwnerTeam)) {
                $this->hasEmenyMines = true;
            }

            if ($defence->isMines) {
                $this->totalMines += $defence->quantity;
            }
        }

        // The mines will attack if 4 conditions are met
        //    1) There is at least 1 group of mines in the sector
        //    2) There is at least 1 mine in the sector 
        //    3) You are not the owner or on the team of the owner - team 0 dosent count
        //    4) You ship is at least $mine_hullsize (setable in config.php) big

        $this->minesAttack = $this->totalMines > 0 && $this->hasEmenyMines && $this->shipAvg() > BalanceEnum::mine_hullsize;

        if ($this->minesAttack && $this->firstDefence->isOwnerTeam) {
            return;
        }

        // Before we had a issue where if there where a lot of mines in the sector the result will go -
        // I changed the behaivor so that rand will chose a % of mines to attack will
        // (it will always be at least 5% of the mines or at the very least 1 mine);
        // and if you are very unlucky they all will hit you

        $pren = (rand(5, 100) / 100);
        $roll = round($pren * $this->totalMines - 1) + 1;

        $this->minesLeft = $roll;

        $minesAttacks = [
            fn() => $this->minesAttackDeflectors(), // If the player has enough mine deflectors then subtract the ammount and continue
            fn() => $this->minesAttackShields(), // Shields up sir!
            fn() => $this->minesAttackArmor(), // Direct hit sir!
            fn() => $this->minesAttackShip(),
        ];

        foreach ($minesAttacks as $mineAttack) {
            $mineAttack();

            if ($this->minesLeft < 1) {
                break;
            }
        }
    }

    protected function minesAttackDeflectors(): void
    {
        if ($this->firstDefence->ship->dev_minedeflector >= $this->minesLeft) {
            $this->lostMineDeflectors = $this->minesLeft;
            $this->minesLeft = 0;
        } else {
            $this->lostMineDeflectors = $this->firstDefence->ship->dev_minedeflector;
            $this->minesLeft = $this->firstDefence->ship->dev_minedeflector;
            $this->lostAllMineDeflectors = $this->firstDefence->ship->dev_minedeflector > 0;
            $this->hadNoMineDeflectors = $this->firstDefence->ship->dev_minedeflector <= 0;
        }
    }

    protected function minesAttackShields(): void
    {
        $playerShields = NUM_SHIELDS($this->ship->shields);

        if ($playerShields > $this->ship->energy) {
            $playerShields = $this->ship->energy;
        }

        if ($playerShields == $this->minesLeft) {
            $this->shieldsAreDown = true;
        }

        if ($playerShields >= $this->minesLeft) {
            $this->lostShipEnergy = $this->minesLeft;
            $this->minesLeft = 0;
        } else {
            $this->lostShipEnergy = $this->ship->energy;
            $this->minesLeft = $this->minesLeft - $playerShields;
        }
    }

    protected function minesAttackArmor(): void
    {
        if ($this->ship->armorPts == $this->minesLeft) {
            $this->hullIsBreached = true;
        }

        if ($this->ship->armorPts >= $this->minesLeft) {
            $this->lostArmorPts = $this->minesLeft;
            $this->minesLeft = 0;
        } else {
            $this->lostArmorPts = $this->ship->armorPts;
            $this->minesLeft = $this->minesLeft - $this->ship->armorPts;
        }
    }

    protected function minesAttackShip(): void
    {
        if ($this->ship->armorPts > 0) {
            $this->shipHasBeenDestroyed = true;
        }
    }

    /**
     *  Compute the ship average...if its too low then the ship will not hit mines...
     * @return mixed
     */
    protected function shipAvg(): mixed
    {
        return array_sum([
            $this->ship->hull,
            $this->ship->engines,
            $this->ship->power,
            $this->ship->computer,
            $this->ship->sensors,
            $this->ship->armor,
            $this->ship->shields,
            $this->ship->beams,
            $this->ship->torpLaunchers,
            $this->ship->cloak
        ]) / 10;
    }

}
