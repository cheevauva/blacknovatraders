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

        $this->messages[] = $this->t([$player->name, 'l_att_att', $target->name]);
        $this->messages[] = $this->t([
            $player->name,
            sprintf('Beams(lvl): %s(%s)', $player->beams, $player->ship['beams']),
            sprintf('Shields(lvl): %s(%s)', $player->shields, $player->ship['shields']),
            sprintf('Energy(Start): %s(%s)', $player->energy, $player->ship['ship_energy']),
            sprintf('Torps(lvl): %s(%s)', $player->torpNum, $player->ship['torp_launchers']),
            sprintf('TorpDmg: %s', $player->torpDmg),
            sprintf('Fighters(lvl): %s', $player->fighters),
            sprintf('Armor(lvl): %s', $player->armorPts, $player->ship['armor']),
            sprintf('Does have Pod? %s', $player->ship['dev_escapepod']),
        ]);

        $this->messages[] = $this->t([
            $target->name,
            sprintf('Beams(lvl): %s(%s)', $target->beams, $target->ship['beams']),
            sprintf('Shields(lvl): %s(%s)', $target->shields, $target->ship['shields']),
            sprintf('Energy(Start): %s(%s)', $target->energy, $target->ship['ship_energy']),
            sprintf('Torps(lvl): %s(%s)', $target->torpNum, $target->ship['torp_launchers']),
            sprintf('TorpDmg: %s', $target->torpDmg),
            sprintf('Fighters(lvl): %s', $target->fighters),
            sprintf('Armor(lvl): %s', $target->armorPts, $target->ship['armor']),
            sprintf('Does have Pod? %s', $target->ship['dev_escapepod']),
        ]);

        $this->messages[] = $this->t('l_att_beams');

        if ($target->fighters > 0 && $player->beams > 0) {
            $this->beamsVsFighters($player, $target);
        }

        if ($player->fighters > 0 && $target->beams > 0) {
            $this->beamsVsFighters($target, $player);
        }

        if ($player->beams > 0) {
            $this->beamsVsShields($player, $target);
        }

        if ($target->beams > 0) {
            $this->beamsVsShields($target, $player);
        }

        if ($player->beams > 0) {
            $this->beamsVsArmor($player, $target);
        }

        if ($target->beams > 0) {
            $this->beamsVsArmor($target, $player);
        }

        $this->messages[] = $this->t('l_att_torps');

        if ($target->fighters > 0 && $player->torpDmg > 0) {
            $this->torpDmgVsFighters($player, $target);
        }

        if ($player->fighters > 0 && $target->torpDmg > 0) {
            $this->torpDmgVsFighters($target, $player);
        }

        if ($player->torpDmg > 0) {
            $this->torpDmgVsArmor($player, $target);
        }

        if ($target->torpDmg > 0) {
            $this->torpDmgVsArmor($target, $player);
        }

        $this->messages[] = $this->t('l_att_fighters');

        if ($player->fighters > 0 && $target->fighters > 0) {
            [$targetFightersLost, $playerFightersLost] = [$this->fightersVsFighters($player, $target), $this->fightersVsFighters($target, $player)];
            //
            $target->lossesInBattle()->fighters($targetFightersLost);
            $target->lossesInBattle()->fighters($playerFightersLost);
        }

        if ($player->fighters > 0) {
            $this->fightersVsArmor($player, $target);
        }

        if ($target->fighters > 0) {
            $this->fightersVsArmor($target, $player);
        }
    }

    protected function beamsVsFighters(Ship $player, Ship $target): void
    {
        $fightersHalf = round($target->fighters / 2);

        if ($player->beams > $fightersHalf) {
            $lost = $target->fighters - $fightersHalf;
        } else {
            $lost = $player->beams;
        }

        $this->messages[] = $this->t([$target->name, 'l_att_lost', $lost, 'l_fighters']);

        $player->lossesInBattle()->beams($lost);
        $target->lossesInBattle()->fighters($lost);
    }

    protected function beamsVsShields(Ship $player, Ship $target): void
    {
        if ($player->beams > $target->shields) {
            $hits = $target->shields;
        } else {
            $hits = $player->beams;
        }

        $this->messages[] = $this->t([$target->name, 'l_att_shits', $hits, 'l_att_dmg']);

        $player->lossesInBattle()->beams($hits);
        $target->lossesInBattle()->shields($hits);
    }

    protected function beamsVsArmor(Ship $player, Ship $target): void
    {
        if ($player->beams > $target->armorPts) {
            $hits = $target->armorPts;
        } else {
            $hits = $player->beams;
        }

        $this->messages[] = $this->t([$target->name, 'l_att_ashit', $hits, 'l_att_dmg']);

        $player->lossesInBattle()->beams($hits);
        $target->lossesInBattle()->armorPts($hits);
    }

    protected function torpDmgVsFighters(Ship $player, Ship $target): void
    {
        $fightersHalf = round($target->fighters / 2);

        if ($player->torpDmg > $fightersHalf) {
            $lost = $target->fighters - $fightersHalf;
        } else {
            $lost = $player->torpDmg;
        }

        $this->messages[] = $this->t([$target->name, 'l_att_lost', $lost, 'l_fighters']);

        $target->lossesInBattle()->fighters($lost);
        $player->lossesInBattle()->torpDmg($lost);
    }

    protected function torpDmgVsArmor(Ship $player, Ship $target): void
    {
        if ($player->torpDmg > $target->armorPts) {
            $lost = $target->armorPts;
        } else {
            $lost = $player->torpDmg;
        }

        $target->lossesInBattle()->armorPts($lost);
    }

    protected function fightersVsFighters(Ship $player, Ship $target): mixed
    {
        if ($player->fighters > $target->fighters) {
            $lost = $target->fighters;
        } else {
            $lost = $player->fighters;
        }

        $this->messages[] = $this->t([$target->name, 'l_att_lost', $lost, 'l_fighters']);

        return $lost;
    }

    protected function fightersVsArmor(Ship $player, Ship $target): void
    {
        if ($player->fighters > $target->armorPts) {
            $lost = $target->armorPts;
        } else {
            $lost = $player->fighters;
        }

        $this->messages[] = $this->t([$target->name, 'l_att_ashit', $lost, 'l_att_dmg']);

        $target->lossesInBattle()->armorPts($lost);
    }
}
