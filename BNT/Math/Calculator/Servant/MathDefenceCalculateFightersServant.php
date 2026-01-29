<?php

declare(strict_types=1);

namespace BNT\Math\Calculator\Servant;

use BNT\Servant;
use BNT\Enum\BalanceEnum;
use BNT\Math\Calculator\DTO\MathCalcDefencesDTO;
use BNT\Math\Calculator\DTO\MathCalcDefenceDTO;
use BNT\Math\Calculator\DTO\MathCalcShipDTO;

class MathDefenceCalculateFightersServant extends Servant
{

    public MathCalcDefencesDTO $defences;
    public MathCalcShipDTO $ship;
    //
    public int $fightersToll = 0;
    public int $totalFighters = 0;
    public bool $hasEmenyFighters = false;
    public bool $shipHasBeenDestroyed = false;
    protected int $fightersLeft = 0;
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

    public function serve(): void
    {
        $this->totalSectorFightes = 0;

        foreach ($this->defences as $defence) {
            $defence = MathCalcDefenceDTO::as($defence);

            if (!$this->hasEmenyFighters && $defence->isFighters && (!$defence->isOwner || !$defence->isOwnerTeam)) {
                $this->hasEmenyFighters = true;
            }

            if ($defence->isFighters) {
                $this->totalSectorFightes += $defence->quantity;
            }
        }

        $this->totalFighters = $this->totalSectorFightes();
        $this->targetFighters = $this->totalFighters;
        $this->playerBeams = $this->calculatePlayerBeams();
        $this->playerShields = $this->calculatePlayerShields();
        $this->playerTorps = $this->calculatePlayerTorps();
        $this->playerTorpDmg = BalanceEnum::torp_dmg_rate->val() * $this->playerTorps;
        $this->playerArmor = $this->ship->armorPts;
        $this->playerFighters = $this->ship->fighters;
        $this->fightersToll = intval(round($this->totalFightes * BalanceEnum::fighter_price->val() * 0.6));

        $figtersAttack = [
            fn() => $this->figtersAttackBeams(),
            fn() => $this->figtersAttackTorps(),
            fn() => $this->figtersAttackFighters(),
            fn() => $this->figtersAttackArmors(),
            fn() => $this->figtersAttackShip(),
        ];

        foreach ($figtersAttack as $figterAttack) {
            $figterAttack();

            if ($this->targetFighters < 1) {
                break;
            }
        }
    }

    private function figtersAttackBeams(): void
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

    private function figtersAttackTorps(): void
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

    private function figtersAttackFighters(): void
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

    private function figtersAttackArmors(): void
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

    private function figtersAttackShip(): void
    {
        $this->shipHasBeenDestroyed = true;
    }

    private function calculatePlayerTorps(): int
    {
        $torp = round(mypw(BalanceEnum::level_factor->val(), $this->ship->torpLaunchers)) * 2;

        if ($torp > $this->ship->torps) {
            $torp = $this->ship->torps;
        }

        return intval($torp);
    }

    private function calculatePlayerShields(): int
    {
        $shields = NUM_SHIELDS($this->ship->shields);

        if ($shields > $this->ship->shields) {
            $shields = $this->ship->shields;
        }

        return intval($shields);
    }

    private function calculatePlayerBeams(): int
    {
        $beams = intval(NUM_BEAMS($this->ship->beams));

        if ($beams > $this->ship->energy) {
            $beams = $this->ship->energy;
        }

        return $beams;
    }

}
