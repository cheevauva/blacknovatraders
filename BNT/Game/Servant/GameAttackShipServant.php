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
use BNT\Game\Servant\GameShipSalvServant;
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
        global $rating_combat_factor;

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
            $this->mt('l_att_flee');
            $this->playerinfoTurn();
            $this->playerinfoUpdate();

            LogPlayerDAO::call($this->container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_OUTMAN, $this->playerinfo['ship_name']);
            return;
        }

        if ($roll > $success) {
            $this->mt('l_planet_noscan');
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
            $dest_sector = rand(1, $sector_max);

            $this->playerinfoTurn();
            $this->playerinfoUpdate();

            LogPlayerDAO::call($this->container, $targetinfo['ship_id'], LogTypeConstants::LOG_ATTACK_EWD, $this->playerinfo['ship_name']);

            $targetinfo['sector'] = $dest_sector;
            $targetinfo['dev_emerwarp'] -= 1;
            $targetinfo['cleared_defences'] = '';

            ShipUpdateDAO::call($this->container, $targetinfo, $targetinfo['ship_id']);
            MovementLogDAO::call($this->container, $targetinfo['ship_id'], $dest_sector);

            $this->mt('l_att_ewd');
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
                $this->mt('l_by_fedbounty2');
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
            $this->shipDestroy($player, $target);
        } else {
            $this->mt('l_att_stilship', [
                'name' => $target->name
            ]);

            $loss = $target->lossesInBattle();

            LogPlayerDAO::call($this->container, $target->id, LogTypeConstants::LOG_ATTACKED_WIN, [$player->name, $loss->armorPts, $loss->fighters]);

            $targetinfo['ship_energy'] = $loss->energy;
            $targetinfo['ship_fighters'] -= $loss->fighters;
            $targetinfo['armor_pts'] -= $loss->armorPts;
            $targetinfo['torps'] -= $loss->numTorps;

            ShipUpdateDAO::call($this->container, $targetinfo, $targetinfo['ship_id']);

            $playerLoss = $player->lossesInBattle();

            $this->playerinfo['ship_energy'] = $playerLoss->energy;
            $this->playerinfo['ship_fighters'] -= $playerLoss->fighters;
            $this->playerinfo['armor_pts'] -= $playerLoss->armorPts;
            $this->playerinfo['torps'] -= $playerLoss->numTorps;
            $this->playerinfoTurn();
            $this->playerinfoUpdate();

            $this->mt([$player->name, $loss->armorPts, 'l_armorpts', $loss->fighters, 'l_fighters', ',', 'l_att_andused', $loss->numTorp, 'l_torps']);
        }

        if ($player->armorPts < 1) {
            $this->playerDestroyed = true;
            $this->shipDestroy($target, $player);
        }
    }

    protected function shipDestroy(Ship $player, Ship $target): void
    {
        $this->mt([$target->name, 'l_att_sdest']);

        if ($target->escapepod == 'Y') {
            $this->mt('l_att_espod');

            ShipRestoreFromEscapePodServant::call($this->container, $target->id);
            LogPlayerDAO::call($this->container, $target->id, LogTypeConstants::LOG_ATTACK_LOSE, [$player->name, 'Y']);
            GameCollectBountyServitor::call($this->container, $player->id, $target->id);
        } else {
            LogPlayerDAO::call($this->container, $target->id, LogTypeConstants::LOG_ATTACK_LOSE, [$player->name, 'N']);
            GameKillPlayerServant::call($this->container, $target->id);
            GameCollectBountyServitor::call($this->container, $player->id, $target->id);
        }

        if ($player->armorPts > 0) {
            $salv = GameShipSalvServant::new($this->container);
            $salv->player = $player;
            $salv->target = $target;
            $salv->serve();

            $loss = $player->lossesInBattle();

            $playerinfo = $player->ship;
            $playerinfo['ship_ore'] += $salv->salvOre;
            $playerinfo['ship_organics'] += $salv->salvOrganics;
            $playerinfo['ship_goods'] += $salv->salvGoods;
            $playerinfo['credits'] += $salv->salvCredits;
            $playerinfo['ship_energy'] -= $loss->energy;
            $playerinfo['ship_fighters'] -= $loss->fighters;
            $playerinfo['armor_pts'] -= $loss->armorPts;
            $playerinfo['torps'] -= $loss->torpNums;
            $playerinfo['turns'] -= 1;
            $playerinfo['turns_used'] += 1;

            ShipUpdateDAO::call($this->container, $playerinfo, $playerinfo);

            $this->mt([$player->name, $loss->armorPts, 'l_armorpts', $loss->fighters, ',', 'l_fighters', 'l_att_andused', $loss->numTorps, 'l_torps']);
        }
    }
}
