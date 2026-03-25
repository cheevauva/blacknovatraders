<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Ship\Ship;

class GameFightShipVersusShipServant extends \UUA\Servant
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
            sprintf('Beams(lvl): %s(%s)', $player->battleState()->beams, $player->beams),
            sprintf('Shields(lvl): %s(%s)', $player->battleState()->shields, $player->shields),
            sprintf('Energy(Start): %s(%s)', $player->energy, $player->energy + $player->battleState()->losses()->energy),
            sprintf('Torps(lvl): %s(%s)', $player->battleState()->numTorp, $player->torp_launchers),
            sprintf('TorpDmg: %s', $player->battleState()->torpDmg),
            sprintf('Fighters(lvl): %s', $player->fighters),
            sprintf('Armor(lvl): %s', $player->armor_pts, $player->armor),
            sprintf('Does have Pod? %s', $player->dev_escapepod),
        ]);

        $this->mt([
            $target->name,
            sprintf('Beams(lvl): %s(%s)', $target->battleState()->beams, $target->beams),
            sprintf('Shields(lvl): %s(%s)', $target->battleState()->shields, $target->shields),
            sprintf('Energy(Start): %s(%s)', $target->energy, $target->energy + $target->battleState()->losses()->energy),
            sprintf('Torps(lvl): %s(%s)', $target->battleState()->numTorp, $target->torp_launchers),
            sprintf('TorpDmg: %s', $target->battleState()->torpDmg),
            sprintf('Fighters(lvl): %s', $target->fighters),
            sprintf('Armor(lvl): %s', $target->armor_pts, $target->armor),
            sprintf('Does have Pod? %s', $target->dev_escapepod),
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
        $target->battleState()->losses()->fighters($targetFightersLost);
        $player->battleState()->losses()->fighters($playerFightersLost);

        $this->fightersVsArmor($player, $target);
        $this->fightersVsArmor($target, $player);
    }

    protected function chargeBeamsAndShield(Ship $player): void
    {
        $beams = $player->battleState()->beams > $player->energy ? $player->energy : $player->battleState()->beams;

        $player->battleState()->losses()->energy($beams);

        $shields = $player->battleState()->shields > $player->energy ? $player->energy : $player->battleState()->shields;

        $player->battleState()->losses()->energy($shields);

        $this->mt([$player->name, 'l_att_charge', $beams, 'l_att_beams']);
        $this->mt([$player->name, 'l_att_charge', $shields, 'l_att_shields']);
    }

    protected function beamsVsFighters(Ship $player, Ship $target): void
    {
        $fightersHalf = round($target->fighters / 2);

        if ($target->fighters <= 0) {
            $lost = 0;
        } elseif ($player->battleState()->beams > $fightersHalf) {
            $lost = $target->fighters - $fightersHalf;
        } else {
            $lost = $player->battleState()->beams;
        }

        $this->mt([$target->name, 'l_att_lost', $lost, 'l_fighters']);

        $player->battleState()->losses()->beams($lost);
        $target->battleState()->losses()->fighters($lost);
    }

    protected function beamsVsShields(Ship $player, Ship $target): void
    {
        if ($target->battleState()->shields <= 0) {
            $hits = 0;
        } elseif ($player->battleState()->beams > $target->battleState()->shields) {
            $hits = $target->battleState()->shields;
        } else {
            $hits = $player->battleState()->beams;
        }

        $this->mt([$target->name, 'l_att_shits', $hits, 'l_att_dmg']);

        $player->battleState()->losses()->beams($hits);
        $target->battleState()->losses()->shields($hits);
    }

    protected function beamsVsArmor(Ship $player, Ship $target): void
    {
        if ($target->armor_pts <= 0) {
            $hits = 0;
        } elseif ($player->battleState()->beams > $target->armor_pts) {
            $hits = $target->armor_pts;
        } else {
            $hits = $player->battleState()->beams;
        }

        $this->mt([$target->name, 'l_att_ashit', $hits, 'l_att_dmg']);

        $target->battleState()->losses()->armorPts($hits);
    }

    protected function torpDmgVsFighters(Ship $player, Ship $target): void
    {
        $fightersHalf = round($target->fighters / 2);

        if ($target->fighters <= 0) {
            $lost = 0;
        } elseif ($player->battleState()->torpDmg > $fightersHalf) {
            $lost = $target->fighters - $fightersHalf;
        } else {
            $lost = $player->battleState()->torpDmg;
        }

        $this->mt([$target->name, 'l_att_lost', $lost, 'l_fighters']);

        $target->battleState()->losses()->fighters($lost);
        $player->battleState()->losses()->torpDmg($lost);
    }

    protected function torpDmgVsArmor(Ship $player, Ship $target): void
    {
        if ($target->armor_pts <= 0) {
            $lost = 0;
        } elseif ($player->battleState()->torpDmg > $target->armor_pts) {
            $lost = $target->armor_pts;
        } else {
            $lost = $player->battleState()->torpDmg;
        }

        $this->mt([$target->name, 'l_att_ashit', $lost, 'l_att_dmg']);

        $target->battleState()->losses()->armorPts($lost);
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
        if ($target->armor_pts <= 0) {
            $lost = 0;
        } elseif ($player->fighters > $target->armor_pts) {
            $lost = $target->armor_pts;
        } else {
            $lost = $player->fighters;
        }

        $this->mt([$target->name, 'l_att_ashit', $lost, 'l_att_dmg']);

        $target->battleState()->losses()->armorPts($lost);
    }
}
