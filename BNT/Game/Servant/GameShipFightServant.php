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

        $this->beamsVsFighters($player, $target);
        $this->beamsVsFighters($target, $player);
        $this->beamsVsShields($player, $target);
        $this->beamsVsShields($target, $player);
        $this->beamsVsArmor($player, $target);
        $this->beamsVsArmor($target, $player);

        $this->messages[] = $this->t('l_att_torps');

        $this->torpDmgVsFighters($player, $target);
        $this->torpDmgVsFighters($target, $player);
        $this->torpDmgVsArmor($player, $target);
        $this->torpDmgVsArmor($target, $player);

        $this->messages[] = $this->t('l_att_fighters');

        [$targetFightersLost, $playerFightersLost] = [$this->fightersVsFighters($player, $target), $this->fightersVsFighters($target, $player)];
        //
        $target->lossesInBattle()->fighters($targetFightersLost);
        $player->lossesInBattle()->fighters($playerFightersLost);

        $this->fightersVsArmor($player, $target);
        $this->fightersVsArmor($target, $player);
    }

    protected function beamsVsFighters(Ship $player, Ship $target): void
    {
        $fightersHalf = round($target->fighters / 2);

        if ($target->fighters <= 0) {
            $lost = 0;
        } elseif ($player->beams > $fightersHalf) {
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
        if ($target->shields <= 0) {
            $hits = 0;
        } elseif ($player->beams > $target->shields) {
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
        if ($target->armorPts <= 0) {
            $hits = 0;
        } elseif ($player->beams > $target->armorPts) {
            $hits = $target->armorPts;
        } else {
            $hits = $player->beams;
        }

        $this->messages[] = $this->t([$target->name, 'l_att_ashit', $hits, 'l_att_dmg']);

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

        $this->messages[] = $this->t([$target->name, 'l_att_lost', $lost, 'l_fighters']);

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

        $this->messages[] = $this->t([$target->name, 'l_att_ashit', $lost, 'l_att_dmg']);

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

        $this->messages[] = $this->t([$target->name, 'l_att_lost', $lost, 'l_fighters']);

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

        $this->messages[] = $this->t([$target->name, 'l_att_ashit', $lost, 'l_att_dmg']);

        $target->lossesInBattle()->armorPts($lost);
    }
}
