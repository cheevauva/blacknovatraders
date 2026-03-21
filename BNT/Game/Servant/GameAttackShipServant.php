<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Ship\DAO\ShipGenScoreDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Ship\Servant\ShipRestoreFromEscapePodServant;
use BNT\Exception\WarningException;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\MovementLog\DAO\MovementLogDAO;
use BNT\Game\Servant\GameCollectBountyServitor;
use BNT\Game\Servant\GameKillPlayerServant;
use BNT\Game\Servant\GameShipFightServant;
use BNT\Game\Servant\GameShipSalvServant;
use BNT\Bounty\DAO\BountyCreateDAO;
use BNT\Bounty\DAO\BountiesByCriteriaDAO;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Zone\DAO\ZoneByIdDAO;

class GameAttackShipServant extends \UUA\Servant
{

    use \BNT\Traits\MessagesTrait;
    use \BNT\Traits\TranslateTrait;

    public array $playerinfo;
    public int $ship;
    public protected(set) bool $playerDestroyed = false;
    public protected(set) bool $targetDestroyed = false;

    protected function roll(): int
    {
        return rand(1, 100);
    }

    protected function roll2(): int
    {
        return rand(1, 100);
    }

    protected function randomValue(): int
    {
        return rand(1, 100);
    }

    #[\Override]
    public function serve(): void
    {
        global $ewd_maxhullsize;
        global $bounty_ratio;
        global $bounty_minturns;

        $target = new Ship(ShipByIdDAO::call($this->container, $this->ship)->ship ?? throw new WarningException()->t(['l_ship', $this->ship, 'l_not_found']));
        $player = new Ship($this->playerinfo);
        $success = (10 - $target->cloak + $player->sensors) * 5;

        $playerscore = ShipGenScoreDAO::call($this->container, $player->id)->score;
        $targetscore = ShipGenScoreDAO::call($this->container, $target->id)->score;
        $playerscore = $playerscore * $playerscore;
        $targetscore = $targetscore * $targetscore;

        if ($success < 5) {
            $success = 5;
        }
        if ($success > 95) {
            $success = 95;
        }

        $flee = (10 - $target->engines + $player->engines) * 5;

        $roll = $this->roll();
        $roll2 = $this->roll2();

        $targetSector = SectorByIdDAO::call($this->container, $target->sector)->sector;
        $targetZone = ZoneByIdDAO::call($this->container, $targetSector['zone_id'])->zone;

        $zoneinfo = $targetZone;

        if ($zoneinfo['allow_attack'] == 'N') {
            $this->mt('l_att_noatt');
            return;
        }

        if ($flee < $roll2) {
            $this->attackOutman($player, $target);
            return;
        }

        if ($roll > $success) {
            $this->attackOutscan($player, $target);
            return;
        }

        $shipScore = shipScore($target->ship);

        if ($shipScore > $ewd_maxhullsize) {
            $chance = ($shipScore - $ewd_maxhullsize) * 10;
        } else {
            $chance = 0;
        }

        if (!empty($target->dev_emerwarp) && $this->randomValue() > $chance) {
            $this->emergencyWarp($player, $target);
            return;
        }

        if (($targetscore / $playerscore < $bounty_ratio || $target->turns_used < $bounty_minturns)) {
            $this->bountyOnAttacker($player, $target, $playerscore);
        }

        if (!empty($target->dev_emerwarp)) {
            LogPlayerDAO::call($this->container, $target->id, LogTypeConstants::LOG_ATTACK_EWDFAIL, $player->name);
        }

        $fight = GameShipFightServant::new($this->container);
        $fight->player = $player;
        $fight->target = $target;
        $fight->serve();

        $this->messagesAppend($fight->messages);

        if ($target->armorPts < 1) {
            $this->targetDestroyed = true;
            $this->shipDestroy($player, $target);
        }

        if ($player->armorPts < 1) {
            $this->playerDestroyed = true;
            $this->shipDestroy($target, $player);
        }

        if ($this->playerDestroyed && $this->targetDestroyed) {
            return;
        }

        if ($this->targetDestroyed && !$this->playerDestroyed) {
            $this->shipCaptureResources($player, $target);
            return;
        }

        if (!$this->targetDestroyed && $this->playerDestroyed) {
            $this->shipCaptureResources($target, $player);
            return;
        }

        $this->happyEnd($player, $target);
    }

    protected function bountyOnAttacker(Ship $attackShip, Ship $underAttackShip, $playerscore): void
    {
        global $bounty_maxvalue;

        // Check to see if there is Federation bounty on the player. If there is, people can attack regardless.
        $btyAmount = array_sum(array_column(BountiesByCriteriaDAO::call($this->container, [
            'placed_by' => 0,
            'bounty_on' => $underAttackShip->id,
        ])->bounties, 'amount'));

        if (!empty($btyAmount)) {
            return;
        }

        $bounty = round($playerscore * $bounty_maxvalue);

        BountyCreateDAO::call($this->container, [
            'bounty_on' => $attackShip->id,
            'placed_by' => 0,
            'amount' => $bounty,
        ]);

        LogPlayerDAO::call($this->container, $attackShip->id, LogTypeConstants::LOG_BOUNTY_FEDBOUNTY, $bounty);

        $this->mt('l_by_fedbounty2');
    }

    protected function attackOutman(Ship $attackShip, Ship $underAttackShip): void
    {
        $attackShipInfo = $attackShip->ship;
        $attackShipInfo['turns'] -= 1;
        $attackShipInfo['turns_used'] += 1;

        ShipUpdateDAO::call($this->container, $attackShipInfo, $attackShip->id);
        LogPlayerDAO::call($this->container, $underAttackShip->id, LogTypeConstants::LOG_ATTACK_OUTMAN, $attackShip->name);

        $this->mt('l_att_flee');
    }

    protected function attackOutscan(Ship $attackShip, Ship $underAttackShip): void
    {
        $attackShipInfo = $attackShip->ship;
        $attackShipInfo['turns'] -= 1;
        $attackShipInfo['turns_used'] += 1;

        ShipUpdateDAO::call($this->container, $attackShipInfo, $attackShip->id);
        LogPlayerDAO::call($this->container, $underAttackShip->id, LogTypeConstants::LOG_ATTACK_OUTSCAN, $attackShip->name);

        $this->mt('l_planet_noscan');
    }

    protected function emergencyWarp(Ship $attackShip, Ship $underAttackShip): void
    {
        global $sector_max;

        $destSector = rand(1, $sector_max);

        $attackShipInfo = $attackShip->ship;
        $attackShipInfo['turns'] -= 1;
        $attackShipInfo['turns_used'] += 1;

        $underAttackShipInfo = $underAttackShip->ship;
        $underAttackShipInfo['sector'] = $destSector;
        $underAttackShipInfo['dev_emerwarp'] -= 1;
        $underAttackShipInfo['cleared_defences'] = '';

        ShipUpdateDAO::call($this->container, $attackShipInfo, $attackShip->id);
        ShipUpdateDAO::call($this->container, $underAttackShipInfo, $underAttackShip->id);
        //
        MovementLogDAO::call($this->container, $underAttackShip->id, $destSector);
        //
        LogPlayerDAO::call($this->container, $underAttackShip->id, LogTypeConstants::LOG_ATTACK_EWD, $attackShip->name);

        $this->mt('l_att_ewd');
    }

    protected function shipCaptureResources(Ship $attackShip, Ship $underAttackShip): void
    {
        $salv = GameShipSalvServant::new($this->container);
        $salv->player = $attackShip;
        $salv->target = $underAttackShip;
        $salv->serve();

        $loss = $attackShip->lossesInBattle();

        $attackShipInfo = $attackShip->ship;
        $attackShipInfo['ship_ore'] += $salv->salvOre;
        $attackShipInfo['ship_organics'] += $salv->salvOrganics;
        $attackShipInfo['ship_goods'] += $salv->salvGoods;
        $attackShipInfo['credits'] += $salv->salvCredits;
        $attackShipInfo['ship_energy'] -= $loss->energy;
        $attackShipInfo['ship_fighters'] -= $loss->fighters;
        $attackShipInfo['armor_pts'] -= $loss->armorPts;
        $attackShipInfo['torps'] -= $loss->torpNums;
        $attackShipInfo['turns'] -= 1;
        $attackShipInfo['turns_used'] += 1;

        ShipUpdateDAO::call($this->container, $attackShipInfo, $attackShip->id);

        $this->mt([$attackShip->name, $loss->armorPts, 'l_armorpts', $loss->fighters, ',', 'l_fighters', 'l_att_andused', $loss->torps, 'l_torps']);
    }

    protected function shipDestroy(Ship $attackShip, Ship $destroyedShip): void
    {
        $this->mt([$destroyedShip->name, 'l_att_sdest']);

        if ($destroyedShip->escapepod == 'Y') {
            $this->mt([$destroyedShip->name, 'l_att_espod']);

            ShipRestoreFromEscapePodServant::call($this->container, $destroyedShip->id);
            LogPlayerDAO::call($this->container, $destroyedShip->id, LogTypeConstants::LOG_ATTACK_LOSE, [$attackShip->name, 'Y']);
            GameCollectBountyServitor::call($this->container, $attackShip->id, $destroyedShip->id);
        } else {
            LogPlayerDAO::call($this->container, $destroyedShip->id, LogTypeConstants::LOG_ATTACK_LOSE, [$attackShip->name, 'N']);
            GameKillPlayerServant::call($this->container, $destroyedShip->id);
            GameCollectBountyServitor::call($this->container, $attackShip->id, $destroyedShip->id);
        }
    }

    protected function happyEnd(Ship $attackShip, Ship $underAttackShip): void
    {
        $this->mt('l_att_stilship', [
            'name' => $underAttackShip->name
        ]);

        $targetLoss = $underAttackShip->lossesInBattle();
        $playerLoss = $attackShip->lossesInBattle();

        $underAttackShipInfo = $underAttackShip->ship;
        $underAttackShipInfo['ship_energy'] = $targetLoss->energy;
        $underAttackShipInfo['ship_fighters'] -= $targetLoss->fighters;
        $underAttackShipInfo['armor_pts'] -= $targetLoss->armorPts;
        $underAttackShipInfo['torps'] -= $targetLoss->torps;

        $attackShipInfo = $attackShip->ship;
        $attackShipInfo['ship_energy'] = $playerLoss->energy;
        $attackShipInfo['ship_fighters'] -= $playerLoss->fighters;
        $attackShipInfo['armor_pts'] -= $playerLoss->armorPts;
        $attackShipInfo['torps'] -= $playerLoss->torps;
        $attackShipInfo['turns'] -= 1;
        $attackShipInfo['turns_used'] += 1;

        ShipUpdateDAO::call($this->container, $underAttackShipInfo, $underAttackShip->id);
        ShipUpdateDAO::call($this->container, $attackShipInfo, $attackShip->id);
        //
        LogPlayerDAO::call($this->container, $underAttackShip->id, LogTypeConstants::LOG_ATTACKED_WIN, [$attackShip->name, $targetLoss->armorPts, $targetLoss->fighters]);

        $this->mt([$attackShip->name, $targetLoss->armorPts, 'l_armorpts', $targetLoss->fighters, 'l_fighters', ',', 'l_att_andused', $targetLoss->torps, 'l_torps']);
    }
}
