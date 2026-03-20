<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Ship\Ship;

class GameShipFightServant extends \UUA\Servant
{

    use \BNT\Traits\TranslateTrait;
    use \BNT\Traits\MessagesTrait;

    public Ship $player;
    public Ship $target;

    #[\Override]
    public function serve(): void
    {
        $player = $this->player;
        $target = $this->target;

        $this->chargeBeamsAndShield($player);
        $this->chargeBeamsAndShield($target);

        $this->mt([$player->name, 'l_att_att', $target->name]);
        $this->mt([
            $player->name,
            sprintf('Beams(lvl): %s(%s)', $player->numBeams, $player->ship['beams']),
            sprintf('Shields(lvl): %s(%s)', $player->numShields, $player->ship['shields']),
            sprintf('Energy(Start): %s(%s)', $player->energy, $player->ship['ship_energy']),
            sprintf('Torps(lvl): %s(%s)', $player->numTorp, $player->ship['torp_launchers']),
            sprintf('TorpDmg: %s', $player->torpDmg),
            sprintf('Fighters(lvl): %s', $player->fighters),
            sprintf('Armor(lvl): %s', $player->armorPts, $player->ship['armor']),
            sprintf('Does have Pod? %s', $player->ship['dev_escapepod']),
        ]);

        $this->mt([
            $target->name,
            sprintf('Beams(lvl): %s(%s)', $target->numBeams, $target->ship['beams']),
            sprintf('Shields(lvl): %s(%s)', $target->numShields, $target->ship['shields']),
            sprintf('Energy(Start): %s(%s)', $target->energy, $target->ship['ship_energy']),
            sprintf('Torps(lvl): %s(%s)', $target->numTorp, $target->ship['torp_launchers']),
            sprintf('TorpDmg: %s', $target->torpDmg),
            sprintf('Fighters(lvl): %s', $target->fighters),
            sprintf('Armor(lvl): %s', $target->armorPts, $target->ship['armor']),
            sprintf('Does have Pod? %s', $target->ship['dev_escapepod']),
        ]);

        $this->mt('l_att_beams');

        $this->beamsVsFighters($player, $target);
        $this->beamsVsFighters($target, $player);
        $this->beamsVsShields($player, $target);
        $this->beamsVsShields($target, $player);
        $this->beamsVsArmor($player, $target);
        $this->beamsVsArmor($target, $player);

        $this->mt('l_att_torps');

        $this->torpDmgVsFighters($player, $target);
        $this->torpDmgVsFighters($target, $player);
        $this->torpDmgVsArmor($player, $target);
        $this->torpDmgVsArmor($target, $player);

        $this->mt('l_att_fighters');

        [$targetFightersLost, $playerFightersLost] = [$this->fightersVsFighters($player, $target), $this->fightersVsFighters($target, $player)];
        //
        $target->lossesInBattle()->fighters($targetFightersLost);
        $player->lossesInBattle()->fighters($playerFightersLost);

        $this->fightersVsArmor($player, $target);
        $this->fightersVsArmor($target, $player);
    }

    protected function chargeBeamsAndShield(Ship $player): void
    {
        $beams = $player->numBeams > $player->energy ? $player->energy : $player->numBeams;

        $player->lossesInBattle()->energy($beams);

        $shields = $player->numShields > $player->energy ? $player->energy : $player->numShields;

        $player->lossesInBattle()->energy($shields);

        $this->mt([$player->name, 'l_att_charge', $beams, 'l_att_beams']);
        $this->mt([$player->name, 'l_att_charge', $shields, 'l_att_shields']);
    }

    protected function beamsVsFighters(Ship $player, Ship $target): void
    {
        $fightersHalf = round($target->fighters / 2);

        if ($target->fighters <= 0) {
            $lost = 0;
        } elseif ($player->numBeams > $fightersHalf) {
            $lost = $target->fighters - $fightersHalf;
        } else {
            $lost = $player->numBeams;
        }

        $this->mt([$target->name, 'l_att_lost', $lost, 'l_fighters']);

        $player->lossesInBattle()->beams($lost);
        $target->lossesInBattle()->fighters($lost);
    }

    protected function beamsVsShields(Ship $player, Ship $target): void
    {
        if ($target->numShields <= 0) {
            $hits = 0;
        } elseif ($player->numBeams > $target->numShields) {
            $hits = $target->numShields;
        } else {
            $hits = $player->numBeams;
        }

        $this->mt([$target->name, 'l_att_shits', $hits, 'l_att_dmg']);

        $player->lossesInBattle()->beams($hits);
        $target->lossesInBattle()->shields($hits);
    }

    protected function beamsVsArmor(Ship $player, Ship $target): void
    {
        if ($target->armorPts <= 0) {
            $hits = 0;
        } elseif ($player->numBeams > $target->armorPts) {
            $hits = $target->armorPts;
        } else {
            $hits = $player->numBeams;
        }

        $this->mt([$target->name, 'l_att_ashit', $hits, 'l_att_dmg']);

        $target->lossesInBattle()->armorPts($hits);
    }

    protected function torpDmgVsFighters(Ship $player, Ship $target): void
    {
        $fightersHalf = round($target->fighters / 2);

        if ($target->fighters <= 0) {
            $lost = 0;
        } elseif ($player->torpDmg > $fightersHalf) {
            $lost = $target->fighters - $fightersHalf;
        } else {
            $lost = $player->torpDmg;
        }

        $this->mt([$target->name, 'l_att_lost', $lost, 'l_fighters']);

        $target->lossesInBattle()->fighters($lost);
        $player->lossesInBattle()->torpDmg($lost);
    }

    protected function torpDmgVsArmor(Ship $player, Ship $target): void
    {
        if ($target->armorPts <= 0) {
            $lost = 0;
        } elseif ($player->torpDmg > $target->armorPts) {
            $lost = $target->armorPts;
        } else {
            $lost = $player->torpDmg;
        }

        $this->mt([$target->name, 'l_att_ashit', $lost, 'l_att_dmg']);

        $target->lossesInBattle()->armorPts($lost);
    }

    protected function fightersVsFighters(Ship $player, Ship $target): mixed
    {
        if ($target->fighters <= 0) {
            $lost = 0;
        } elseif ($player->fighters > $target->fighters) {
            $lost = $target->fighters;
        } else {
            $lost = $player->fighters;
        }

        $this->mt([$target->name, 'l_att_lost', $lost, 'l_fighters']);

        return $lost;
    }

    protected function fightersVsArmor(Ship $player, Ship $target): void
    {
        if ($target->armorPts <= 0) {
            $lost = 0;
        } elseif ($player->fighters > $target->armorPts) {
            $lost = $target->armorPts;
        } else {
            $lost = $player->fighters;
        }

        $this->mt([$target->name, 'l_att_ashit', $lost, 'l_att_dmg']);

        $target->lossesInBattle()->armorPts($lost);
    }
}
