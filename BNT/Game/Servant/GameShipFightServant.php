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

        $this->messages[] = $this->t(['l_att_att', $target->name]);
        $this->messages[] = $this->t([
            'You',
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
            'Target',
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
            $player->torpDmgVsFighters($player, $target);
        }

        if ($player->fighters > 0 && $target->torpDmg > 0) {
            $target->torpDmgVsFighters($target, $player);
        }

        if ($player->torpDmg > 0) {
            $player->torpDmgVsArmor($player, $target);
        }

        if ($target->torpDmg > 0) {
            $target->torpDmgVsArmor($target, $player);
        }

        $this->messages[] = $this->t('l_att_fighters');

        if ($player->fighters > 0 && $target->fighters > 0) {
            [$targetFighters, $playerFighters] = [$this->fightersVsFighters($player, $target), $this->fightersVsFighters($target, $player)];
            //
            $target->fighters = $targetFighters;
            $player->fighters = $playerFighters;
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
        $temp = round($target->fighters / 2);

        if ($player->beams > $temp) {
            $lost = $target->fighters - $temp;
            $player->beams -= $lost;
            $target->fighters = $temp;
            $this->messages[] = $this->t([$target->name, 'l_att_lost', $lost, 'l_fighters']);
        } else {
            $target->fighters -= $player->beams;
            $this->messages[] = $this->t([$target->name, 'l_att_lost', $player->beams, 'l_fighters']);
            $player->beams = 0;
        }
    }

    protected function beamsVsShields(Ship $player, Ship $target): void
    {
        if ($player->beams > $target->shields) {
            $target->shields = 0;
            $player->beams -= $target->shields;
            $this->messages[] = $this->t([$target->name, 'l_att_sdown']);
        } else {
            $target->shields -= $player->beams;
            $this->messages[] = $this->t([$target->name, 'l_att_shits', $player->beams, 'l_att_dmg']);
            $player->beams = 0;
        }
    }

    protected function beamsVsArmor(Ship $player, Ship $target): void
    {
        if ($player->beams > $target->armorPts) {
            $target->armorPts = 0;
            $this->messages[] = $this->t([$target->name, 'l_att_sarm']);
        } else {
            $target->armorPts -= $player->beams;
            $this->messages[] = $this->t([$target->name, 'l_att_ashit', $player->beams, 'l_att_dmg']);
        }
    }

    protected function torpDmgVsFighters(Ship $player, Ship $target): void
    {
        $temp = round($target->fighters / 2);

        if ($player->torpDmg > $temp) {
            $lost = $target->fighters - $temp;
            $this->messages[] = $this->t([$target->name, 'l_att_lost', $lost, 'l_fighters']);
            $target->fighters = $temp;
            $player->torpDmg -= $lost;
        } else {
            $target->fighters -= $player->torpDmg;
            $this->messages[] = $this->t([$target->name, 'l_att_lost', $player->torpDmg, 'l_fighters']);
            $player->torpDmg = 0;
        }
    }

    protected function torpDmgVsArmor(Ship $player, Ship $target): void
    {
        if ($player->torpDmg > $target->armorPts) {
            $target->armorPts = 0;
            $this->messages[] = $this->t([$target->name, 'l_att_sarm']);
        } else {
            $target->armorPts -= $player->torpDmg;
            $this->messages[] = $this->t([$target->name, 'l_att_ashit', $player->torpDmg, 'l_att_dmg']);
        }
    }

    protected function fightersVsFighters(Ship $player, Ship $target): mixed
    {
        if ($player->fighters > $target->fighters) {
            $this->messages[] = $this->t([$target->name, 'l_att_lostf']);
            return 0;
        } else {
            $this->messages[] = $this->t([$target->name, 'l_att_lost', $player->fighters, 'l_fighters']);
            return $target->fighters - $player->fighters;
        }
    }

    protected function fightersVsArmor(Ship $player, Ship $target): mixed
    {
        if ($player->fighters > $target->armorPts) {
            $target->armorPts = 0;
            $this->messages[] = $this->t([$target->name, 'l_att_sarm']);
        } else {
            $target->armor = $target->armorPts - $player->fighters;
            $this->messages[] = $this->t([$target->name, 'l_att_ashit', $player->fighters, 'l_att_dmg']);
        }
    }
}
