<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Exception\WarningException;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\MovementLog\DAO\MovementLogDAO;
use BNT\Ship\Servant\ShipRestoreFromEscapePodServant;
use BNT\Game\Servant\GameCollectBountyServitor;
use BNT\Game\Servant\GameKillPlayerServant;
use BNT\Bounty\DAO\BountyCreateDAO;
use BNT\Game\Servant\GameShipFightServant;
use BNT\Ship\Ship;

class GameAttackShipServant extends \UUA\Servant
{

    use \BNT\Traits\PlayerinfoTrait;
    use \BNT\Traits\MessagesTrait;
    use \BNT\Traits\TranslateTrait;

    public int $ship;
    public protected(set) bool $playerDestroyed = false;
    public protected(set) bool $targetDestroyed = false;

    #[\Override]
    public function serve(): void
    {
        global $sector_max;
        global $ewd_maxhullsize;
        global $bounty_maxvalue;
        global $bounty_ratio;
        global $bounty_minturns;

        $targetinfo = ShipByIdDAO::call($this->container, $this->ship)->ship;
        $success = (10 - $targetinfo['cloak'] + $this->playerinfo['sensors']) * 5;

        $playerscore = gen_score($this->playerinfo['ship_id']);
        $targetscore = gen_score($targetinfo['ship_id']);
        $playerscore = $playerscore * $playerscore;
        $targetscore = $targetscore * $targetscore;

        if ($success < 5) {
            $success = 5;
        }
        if ($success > 95) {
            $success = 95;
        }

        $flee = (10 - $targetinfo['engines'] + $this->playerinfo['engines']) * 5;
        $roll = rand(1, 100);
        $roll2 = rand(1, 100);

        $targetSector = SectorByIdDAO::call($this->container, $targetinfo['sector'])->sector;
        $targetZone = ZoneByIdDAO::call($this->container, $targetSector['zone_id'])->zone;

        $zoneinfo = $targetZone;

        if ($zoneinfo['allow_attack'] == 'N') {
            throw new WarningException('l_att_noatt');
        }

        if ($flee < $roll2) {
            $this->messages[] = $this->t('l_att_flee');
            $this->playerinfoTurn();
            $this->playerinfoUpdate();

            LogPlayerDAO::call($this->container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_OUTMAN, $this->playerinfo['ship_name']);
            return;
        }

        if ($roll > $success) {
            $this->messages[] = $this->t('l_planet_noscan');
            $this->playerinfoTurn();
            $this->playerinfoUpdate();
            LogPlayerDAO::call($this->container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_OUTSCAN, $this->playerinfo['ship_name']);
            return;
        }

        $shipavg = shipScore($targetinfo);

        if ($shipavg > $ewd_maxhullsize) {
            $chance = ($shipavg - $ewd_maxhullsize) * 10;
        } else {
            $chance = 0;
        }

        $random_value = rand(1, 100);

        if (!empty($targetinfo['dev_emerwarp']) && $random_value > $chance) {
            $rating_change = round($targetinfo['rating'] * .1);
            $dest_sector = rand(1, $sector_max);

            $this->playerinfo['rating'] -= $rating_change;
            $this->playerinfoTurn();
            $this->playerinfoUpdate();

            LogPlayerDAO::call($this->container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_EWD, $this->playerinfo['ship_name']);

            $targetinfo['sector'] = $dest_sector;
            $targetinfo['dev_emerwarp'] -= 1;
            $targetinfo['cleared_defences'] = '';

            ShipUpdateDAO::call($this->container, $targetinfo, $targetinfo['ship_id']);
            MovementLogDAO::call($this->container, $targetinfo['ship_id'], $dest_sector);

            $this->messages[] = $this->t('l_att_ewd');
            return;
        }

        if (($targetscore / $playerscore < $bounty_ratio || $targetinfo['turns_used'] < $bounty_minturns)) {
            // Check to see if there is Federation bounty on the player. If there is, people can attack regardless.
            $btyamount = db()->fetch("SELECT SUM(amount) AS btytotal FROM bounty WHERE bounty_on = :ship_id AND placed_by = 0", [
                'ship_id' => $targetinfo['ship_id']
            ]);
            if ($btyamount <= 0) {
                $bounty = round($playerscore * $bounty_maxvalue);
                BountyCreateDAO::call($this->container, [
                    'bounty_on' => $this->playerinfo['ship_id'],
                    'placed_by' => 0,
                    'amount' => $bounty,
                ]);
                LogPlayerDAO::call($this->container, $this->playerinfo['ship_id'], LogTypeConstants::LOG_BOUNTY_FEDBOUNTY, $bounty);
                $this->messages[] = $this->t('l_by_fedbounty2');
            }
        }

        if (!empty($targetinfo['dev_emerwarp'])) {
            LogPlayerDAO::call($this->container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_EWDFAIL, $this->playerinfo['ship_name']);
        }

        $target = new Ship($targetinfo);
        $player = new Ship($this->playerinfo);

        $fight = GameShipFightServant::new($this->container);
        $fight->player = $player;
        $fight->target = $target;
        $fight->serve();

        $this->messagesAppend($fight->messages);

        if ($target->armorPts < 1) {
            $this->targetDestroyed = true;

            $this->messages[] = $this->t([$targetinfo['ship_name'], 'l_att_sdest']);

            if ($targetinfo['dev_escapepod'] == "Y") {
                $this->messages[] = $this->t('l_att_espod');

                $targetinfo['rating'] /= 2;

                ShipRestoreFromEscapePodServant::call($this->container, $targetinfo);
                LogPlayerDAO::call($this->container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_LOSE, [$this->playerinfo['ship_name'], 'Y']);
                GameCollectBountyServitor::call($this->container, $this->playerinfo['ship_id'], $targetinfo['ship_id']);
            } else {
                LogPlayerDAO::call($this->container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_LOSE, [$this->playerinfo['ship_name'], 'N']);
                GameKillPlayerServant::call($this->container, $targetinfo['ship_id']);
                GameCollectBountyServitor::call($this->container, $this->playerinfo['ship_id'], $targetinfo['ship_id']);
            }

            if ($player->armorPts > 0) {
                $rating_change = round($targetinfo['rating'] * $rating_combat_factor);

                $this->playerinfo = $this->calculateShip($targetinfo, $this->playerinfo, true, $target->armorPts, $target->fighters, $target->torpNum);
                $this->playerinfo['rating'] -= $rating_change;
                $this->playerinfoTurn();
                $this->playerinfoUpdate();

                $this->messages[] = $this->t(['l_att_ylost', $armor_lost, 'l_armorpts', $fighters_lost, ',', 'l_fighters', 'l_att_andused', $player->torpNum, 'l_torps']);
            }
        } else {
            $this->messages[] = $this->t('l_att_stilship', [
                'name' => $targetinfo['ship_name'],
            ]);

            $rating_change = round($targetinfo['rating'] * .1);
            $targetArmorLost = $targetinfo['armor_pts'] - $target->armorPts;
            $targetFightersLost = $targetinfo['ship_fighters'] - $target->fighters;

            LogPlayerDAO::call($this->container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACKED_WIN, [
                $this->playerinfo['ship_name'],
                $targetArmorLost,
                $targetFightersLost
            ]);

            $targetinfo['ship_fighters'] -= $targetFightersLost;
            $targetinfo['armor_pts'] -= $targetArmorLost;
            $targetinfo['torps'] -= $target->torpNum;

            ShipUpdateDAO::call($this->container, $targetinfo, $targetinfo['ship_id']);

            $playerArmorLost = $this->playerinfo['armor_pts'] - $player->armorPts;
            $playerFightersLost = $this->playerinfo['ship_fighters'] - $player->fighters;

            $this->playerinfo['ship_energy'] = $energy;
            $this->playerinfo['ship_fighters'] -= $playerFightersLost;
            $this->playerinfo['armor_pts'] -= $playerArmorLost;
            $this->playerinfo['torps'] -= $player->torpNum;
            $this->playerinfo['rating'] -= $rating_change;
            $this->playerinfoTurn();
            $this->playerinfoUpdate();

            $this->messages[] = $this->t(['l_att_ylost', $armor_lost, 'l_armorpts', $fighters_lost, 'l_fighters', ',', 'l_att_andused', $player->torpNum, 'l_torps']);
        }

        if ($player->armorPts < 1) {
            $this->playerDestroyed = true;

            $this->messages[] = $this->t('l_att_yshiplost');

            if ($this->playerinfo['dev_escapepod'] == "Y") {
                $this->messages[] = $this->t('l_att_loosepod');

                $this->playerinfo['rating'] /= 2;

                ShipRestoreFromEscapePodServant::call($this->container, $this->playerinfo);
                GameCollectBountyServitor::call($this->container, $targetinfo['ship_id'], $this->playerinfo['ship_id']);
            } else {
                $this->messages[] = 'Didnt have pod?! ' . $this->playerinfo['dev_escapepod'];
                GameKillPlayerServant::call($this->container, $this->playerinfo['ship_id']);
                GameCollectBountyServitor::call($this->container, $targetinfo['ship_id'], $this->playerinfo['ship_id']);
            }

            if ($target->armorPts > 0) {
                $targetinfo = $this->calculateShip($this->playerinfo, $targetinfo, false, $target->armorPts, $target->fighters, $target->torpNum, $salv_credits);

                ShipUpdateDAO::call($this->container, $targetinfo, $targetinfo['ship_id']);
            }
        }
    }

    protected function calculatePlayerinfo(array $ship, $armor, $fighters, $torpNum): array
    {
        $ship = $this->calculateShip($ship, true, $armor, $fighters, $torpNum);
        $ship['turns'] -= 1;
        $ship['turns_used'] += 1;
        $ship['rating'] -= $rating_change;

        return $ship;
    }

    protected function calculateShip(array $ship1, array $ship2, bool $attacker, $armor, $fighters, $torpNum): array
    {
        global $upgrade_factor;
        global $upgrade_cost;

        $free_ore = round($ship1['ship_ore'] / 2);
        $free_organics = round($ship1['ship_organics'] / 2);
        $free_goods = round($ship1['ship_goods'] / 2);
        $free_holds = NUM_HOLDS($ship2['hull']) - $ship2['ship_ore'] - $ship2['ship_organics'] - $ship2['ship_goods'] - $ship2['ship_colonists'];

        if ($free_holds > $free_goods) {
            $salv_goods = $free_goods;
            $free_holds = $free_holds - $free_goods;
        } elseif ($free_holds > 0) {
            $salv_goods = $free_holds;
            $free_holds = 0;
        } else {
            $salv_goods = 0;
        }

        if ($free_holds > $free_ore) {
            $salv_ore = $free_ore;
            $free_holds = $free_holds - $free_ore;
        } elseif ($free_holds > 0) {
            $salv_ore = $free_holds;
            $free_holds = 0;
        } else {
            $salv_ore = 0;
        }

        if ($free_holds > $free_organics) {
            $salv_organics = $free_organics;
            $free_holds = $free_holds - $free_organics;
        } elseif ($free_holds > 0) {
            $salv_organics = $free_holds;
            $free_holds = 0;
        } else {
            $salv_organics = 0;
        }

        $ship_value = $upgrade_cost * array_sum([
            round(mypw($upgrade_factor, $ship1['hull'])),
            round(mypw($upgrade_factor, $ship1['engines'])),
            round(mypw($upgrade_factor, $ship1['power'])),
            round(mypw($upgrade_factor, $ship1['computer'])),
            round(mypw($upgrade_factor, $ship1['sensors'])),
            round(mypw($upgrade_factor, $ship1['beams'])),
            round(mypw($upgrade_factor, $ship1['torp_launchers'])),
            round(mypw($upgrade_factor, $ship1['shields'])),
            round(mypw($upgrade_factor, $ship1['armor'])),
            round(mypw($upgrade_factor, $ship1['cloak']))
        ]);
        $ship_salvage_rate = rand(10, 20);
        $ship_salvage = $ship_value * $ship_salvage_rate / 100;

        $armor_lost = $ship2['armor_pts'] - $armor;
        $fighters_lost = $ship2['ship_fighters'] - $fighters;
        $energy = $ship2['ship_energy'];

        $ship2['ship_ore'] += $salv_ore;
        $ship2['ship_organics'] += $salv_organics;
        $ship2['ship_goods'] += $salv_goods;
        $ship2['credits'] += $ship_salvage;
        $ship2['ship_energy'] = $energy;
        $ship2['ship_fighters'] -= $fighters_lost;
        $ship2['armor_pts'] -= $armor_lost;
        $ship2['torps'] -= $torpNum;

        $this->messages[] = $this->t($attacker ? 'l_att_ysalv' : 'l_att_salv', [
            'salv_ore' => $salv_ore,
            'salv_organics' => $salv_organics,
            'salv_goods' => $salv_goods,
            'ship_salvage_rate' => $ship_salvage_rate,
            'ship_salvage' => $ship_salvage,
            'name' => $ship2['ship_name'],
            'rating_change' => number(abs($rating_change)),
        ]);

        return $ship;
    }
}
