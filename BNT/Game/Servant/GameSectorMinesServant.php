<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Translate;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Game\Servant\GameMessageDefenceOwnerServant;
use BNT\Game\Servant\GameExplodeMinesServant;
use BNT\Game\Servant\GameCancelBountyServant;
use BNT\Game\Servant\GameKillPlayerServant;
use BNT\Ship\Servant\ShipRestoreFromEscapePodServant;

class GameSectorMinesServant extends \UUA\Servant
{

    use \BNT\Traits\TranslateTrait;

    public array $playerinfo;
    public int $sector;
    public int $totalSectorMines;
    public protected(set) array $messages = [];
    public protected(set) bool $shipDestroyed = false;

    #[\Override]
    public function serve(): void
    {
        $roll = $this->roll();

        // Red Alert: You are hit sir!!! Tell the player and put it in the log
        $this->messages[] = $this->t('l_chm_youhitsomemines', [
            'chm_roll' => $roll,
        ]);

        LogPlayerDAO::call($this->container, $this->playerinfo['ship_id'], LogTypeConstants::LOG_HIT_MINES, [$roll, $this->sector]);

        // Tell the owner that his mines where hit
        GameMessageDefenceOwnerServant::call($this->container, $this->sector, $this->t('l_chm_hehitminesinsector', [
            'chm_playerinfo_character_name' => $this->playerinfo['ship_name'],
            'chm_roll' => $roll,
            'chm_sector' => $this->sector,
        ]));

        // If the player has enough mine deflectors then subtract the ammount and continue
        if ($this->playerinfo['dev_minedeflector'] >= $roll) {
            $this->messages[] = $this->t('l_chm_youlostminedeflectors', ['chm_roll' => $roll]);

            $this->playerinfo['dev_minedeflector'] -= $roll;
            $this->playerinfoUpdate();

            GameExplodeMinesServant::call($this->container, $this->sector, $roll);
            return;
        }

        $this->messages[] = $this->playerinfo['dev_minedeflector'] > 0 ? $this->t('l_chm_youlostallminedeflectors') : $this->t('l_chm_youhadnominedeflectors');

        // Shields up sir!
        $minesLeft = $roll - $this->playerinfo['dev_minedeflector'];
        $playerShields = NUM_SHIELDS($this->playerinfo['shields']);

        if ($playerShields > $this->playerinfo['ship_energy']) {
            $playerShields = $this->playerinfo['ship_energy'];
        }

        if ($playerShields >= $minesLeft) {
            $this->messages[] = $this->t('l_chm_yourshieldshitforminesdmg', [
                'chm_mines_left' => $minesLeft,
            ]);

            $this->playerinfo['ship_energy'] -= $minesLeft;
            $this->playerinfo['dev_minedeflector'] = 0;
            $this->playerinfoUpdate();

            if ($playerShields == $minesLeft) {
                $this->messages[] = $this->t('l_chm_yourshieldsaredown');
            }

            GameExplodeMinesServant::call($this->container, $this->sector, $roll);
            return;
        }

        // Direct hit sir
        $this->messages[] = $this->t('l_chm_youlostallyourshields');
        $minesLeft -= $playerShields;

        if ($this->playerinfo['armor_pts'] >= $minesLeft) {
            $this->messages[] = $this->t('l_chm_yourarmorhitforminesdmg', [
                'chm_mines_left' => $minesLeft,
            ]);

            $this->playerinfo['armor_pts'] -= $minesLeft;
            $this->playerinfo['ship_energy'] = 0;
            $this->playerinfo['dev_minedeflector'] = 0;
            $this->playerinfoUpdate();

            if ($this->playerinfo['armor_pts'] == $minesLeft) {
                $this->messages[] = $this->t('l_chm_yourhullisbreached');
            }
            GameExplodeMinesServant::call($this->container, $this->sector, $roll);
            return;
        }

        $this->shipDestroyed = true;

        // BOOM
        LogPlayerDAO::call($this->container, $this->playerinfo['ship_id'], LogTypeConstants::LOG_SHIP_DESTROYED_MINES, [$this->sector, $this->playerinfo['dev_escapepod']]);

        GameMessageDefenceOwnerServant::call($this->container, $this->sector, $this->t('l_chm_hewasdestroyedbyyourmines', [
            'chm_playerinfo_character_name' => $this->playerinfo['ship_name'],
            'chm_sector' => $this->sector,
        ]));

        $this->messages[] = $this->t('l_chm_yourshiphasbeendestroyed');

        // Live...
        if ($this->playerinfo['dev_escapepod'] == 'Y') {
            $this->playerinfo['rating'] = round($this->playerinfo['rating'] / 2);
            $this->messages[] = $this->t('l_chm_luckescapepod');
            ShipRestoreFromEscapePodServant::call($this->container, $this->playerinfo);
            GameCancelBountyServant::call($this->container, $this->playerinfo['ship_id']);
        } else {
            // or die!
            GameCancelBountyServant::call($this->container, $this->playerinfo['ship_id']);
            GameKillPlayerServant::call($this->container, $this->playerinfo['ship_id']);
        }

        GameExplodeMinesServant::call($this->container, $this->sector, $roll);
    }

    protected function roll(): int
    {
        // Well...you hit mines, shame...
        // New Behaivor
        // Before we had a issue where if there where a lot of mines in the sector the result will go -
        // I changed the behaivor so that rand will chose a % of mines to attack will
        // (it will always be at least 5% of the mines or at the very least 1 mine);
        // and if you are very unlucky they all will hit you
        return (int) round((rand(5, 100) / 100) * $this->totalSectorMines - 1) + 1;
    }

    protected function playerinfoUpdate(): void
    {
        ShipUpdateDAO::call($this->container, $this->playerinfo, $this->playerinfo['ship_id']);
    }
}
