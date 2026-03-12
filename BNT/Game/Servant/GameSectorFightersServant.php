<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Translate;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Ship\Servant\ShipRestoreFromEscapePodServant;
use BNT\Game\Servant\GameDestroyFightersServant;
use BNT\Game\Servant\GameMessageDefenceOwnerServant;
use BNT\Game\Servant\GameKillPlayerServant;
use BNT\Game\Servant\GameCancelBountyServant;

class GameSectorFightersServant extends \UUA\Servant
{

    public array $playerinfo;
    public int $totalSectorFighters;
    public bool $isRSMove = false;
    public float|int $energyScooped;
    public protected(set) array $messages = [];
    public protected(set) bool $shipDestroyed = false;

    #[\Override]
    public function serve(): void
    {
        global $level_factor;
        global $torp_dmg_rate;

        $this->messages[] = $this->t('l_sf_attacking');

        $targetFighters = $this->totalSectorFighters;
        $playerBeams = NUM_BEAMS($this->playerinfo['beams']);

        if ($this->isRSMove) {
            $this->playerinfo['ship_energy'] += $this->energyScooped;
        }

        if ($playerBeams > $this->playerinfo['ship_energy']) {
            $playerBeams = $this->playerinfo['ship_energy'];
        }

        $this->playerinfo['ship_energy'] = $this->playerinfo['ship_energy'] - $playerBeams;
        $playerShields = NUM_SHIELDS($this->playerinfo['shields']);

        if ($playerShields > $this->playerinfo['ship_energy']) {
            $playerShields = $this->playerinfo['ship_energy'];
        }

        // $this->playerinfo['ship_energy']=$this->playerinfo['ship_energy']-$playershields;
        $playerTorpNum = round(mypw($level_factor, $this->playerinfo['torp_launchers'])) * 2;

        if ($playerTorpNum > $this->playerinfo['torps']) {
            $playerTorpNum = $this->playerinfo['torps'];
        }

        $playerTorpDmg = $torp_dmg_rate * $playerTorpNum;
        $playerArmor = $this->playerinfo['armor_pts'];
        $playerFighters = $this->playerinfo['ship_fighters'];

        if ($targetFighters > 0 && $playerBeams > 0) {
            if ($playerBeams > round($targetFighters / 2)) {
                $temp = round($targetFighters / 2);
                $lost = $targetFighters - $temp;
                $this->messages[] = $this->t('l_sf_destfight', ['lost' => $lost]);
                $targetFighters = $temp;
                $playerBeams = $playerBeams - $lost;
            } else {
                $targetFighters = $targetFighters - $playerBeams;
                $this->messages[] = $this->t('l_sf_destfightb', ['lost' => $playerBeams]);
                $playerBeams = 0;
            }
        }

        $this->messages[] = $this->t('l_sf_torphit');

        if ($targetFighters > 0 && $playerTorpDmg > 0) {
            if ($playerTorpDmg > round($targetFighters / 2)) {
                $temp = round($targetFighters / 2);
                $lost = $targetFighters - $temp;
                $this->messages[] = $this->t('l_sf_destfightt', ['lost' => $lost]);
                $targetFighters = $temp;
                $playerTorpDmg = $playerTorpDmg - $lost;
            } else {
                $targetFighters = $targetFighters - $playerTorpDmg;
                $this->messages[] = $this->t('l_sf_destfightt', ['lost' => $playerTorpDmg]);
                $playerTorpDmg = 0;
            }
        }

        $this->messages[] = $this->t('l_sf_fighthit');

        if ($playerFighters > 0 && $targetFighters > 0) {
            if ($playerFighters > $targetFighters) {
                $this->messages[] = $this->t('l_sf_destfightall');
                $temptargfighters = 0;
            } else {
                $this->messages[] = $this->t('l_sf_destfightt2', ['lost' => $playerFighters]);
                $temptargfighters = $targetFighters - $playerFighters;
            }

            if ($targetFighters > $playerFighters) {
                $this->messages[] = $this->t('l_sf_lostfight');
                $tempplayfighters = 0;
            } else {
                $this->messages[] = $this->t('l_sf_lostfight2', ['lost' => $targetFighters]);
                $tempplayfighters = $playerFighters - $targetFighters;
            }

            $playerFighters = $tempplayfighters;
            $targetFighters = $temptargfighters;
        }

        if ($targetFighters > 0) {
            if ($targetFighters > $playerArmor) {
                $playerArmor = 0;
                $this->messages[] = $this->t('l_sf_armorbreach');
            } else {
                $playerArmor = $playerArmor - $targetFighters;
                $this->messages[] = $this->t('l_sf_armorbreach2', ['lost' => $targetFighters]);
            }
        }

        $fightersLost = $this->totalSectorFighters - $targetFighters;

        GameDestroyFightersServant::call($this->container, $this->sector, $fightersLost);
        GameMessageDefenceOwnerServant::new($this->container, $this->sector, $this->t('l_sf_sendlog', [
            'player' => $this->playerinfo['ship_name'],
            'lost' => $fightersLost,
            'sector' => $this->sector
        ]));

        LogPlayerDAO::call($this->container, $this->playerinfo['ship_id'], LogTypeConstants::LOG_DEFS_DESTROYED_F, [
            $fightersLost,
            $this->sector
        ]);

        $armor_lost = $this->playerinfo['armor_pts'] - $playerArmor;
        $fighters_lost = $this->playerinfo['ship_fighters'] - $playerFighters;
        $energy = $this->playerinfo['ship_energy'];

        $this->playerinfo['ship_energy'] = $energy;
        $this->playerinfo['ship_fighters'] -= $fighters_lost;
        $this->playerinfo['armor_pts'] -= $armor_lost;
        $this->playerinfo['torps'] -= $playerTorpNum;

        ShipUpdateDAO::call($this->container, $this->playerinfo, $this->playerinfo['ship_id']);

        $this->messages[] = $this->t('l_sf_lreport', [
            'armor' => $armor_lost,
            'fighters' => $fighters_lost,
            'torps' => $playerTorpNum
        ]);

        if ($playerArmor < 1) {
            $this->shipDestroyed = true;

            $this->messages[] = $this->t('l_sf_shipdestroyed');

            LogPlayerDAO::call($this->container, $this->playerinfo['ship_id'], LogTypeConstants::LOG_DEFS_KABOOM, [
                $this->sector,
                $this->playerinfo['dev_escapepod']
            ]);

            GameMessageDefenceOwnerServant::call($this->container, $this->sector, $this->t('l_sf_sendlog2', [
                'player' => $this->playerinfo['ship_name'],
                'sector' => $this->sector
            ]));

            if ($this->playerinfo['dev_escapepod'] == 'Y') {
                $this->messages[] = $this->t('l_sf_escape');
                $this->playerinfo['rating'] = round($this->playerinfo['rating'] / 2);
                ShipRestoreFromEscapePodServant::call($this->container, $this->playerinfo);
                GameCancelBountyServant::call($this->container, $this->playerinfo['ship_id']);
            } else {
                GameCancelBountyServant::call($this->container, $this->playerinfo['ship_id']);
                GameKillPlayerServant::call($this->container, $this->playerinfo['ship_id']);
            }
        }
    }

    protected function t(array|string $tag, array $replace = [], ?string $format = null): Translate
    {
        return new Translate()->translate($tag, $replace, $format);
    }
}
