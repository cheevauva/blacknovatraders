<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipGenScoreDAO;
use BNT\Ship\Servant\ShipRestoreFromEscapePodServant;
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
use BNT\Ship\Servant\ShipSaveServant;
use BNT\Ship\Mapper\ShipRowToEntityMapper;
use BNT\Game\Servant\GameShipScanShipServant;
use BNT\Game\Servant\GameShipEscapedFromShipServant;
use BNT\Game\Servant\GameShipEmergencyWarpServant;

class GameAttackShipServant extends \UUA\Servant
{

    use \BNT\Traits\MessagesTrait;
    use \BNT\Traits\TranslateTrait;

    public array $playerinfo;
    public array $targetinfo;
    public int $ship;
    public protected(set) bool $playerDestroyed = false;
    public protected(set) bool $targetDestroyed = false;

    #[\Override]
    public function serve(): void
    {
        global $bounty_ratio;
        global $bounty_minturns;

        $target = ShipRowToEntityMapper::call($this->container, $this->targetinfo)->ship;
        $player = ShipRowToEntityMapper::call($this->container, $this->playerinfo)->ship;

        $playerscore = ShipGenScoreDAO::call($this->container, $player->id)->score;
        $targetscore = ShipGenScoreDAO::call($this->container, $target->id)->score;
        $playerscore = $playerscore * $playerscore;
        $targetscore = $targetscore * $targetscore;

        $targetSector = SectorByIdDAO::call($this->container, $target->sector)->sector;
        $targetZone = ZoneByIdDAO::call($this->container, $targetSector['zone_id'])->zone;

        $zoneinfo = $targetZone;

        if ($zoneinfo['allow_attack'] == 'N') {
            $this->mt('l_att_noatt');
            return;
        }

        $escapeFromShip = GameShipEscapedFromShipServant::new($this->container);
        $escapeFromShip->base = 10;
        $escapeFromShip->player = $player;
        $escapeFromShip->target = $target;
        $escapeFromShip->serve();

        if ($escapeFromShip->isSuccess) {
            $this->attackOutman($player, $target);
            return;
        }

        $scan = GameShipScanShipServant::new($this->container);
        $scan->base = 10;
        $scan->player = $player;
        $scan->target = $target;
        $scan->serve();

        if (!$scan->isSuccess) {
            $this->attackOutscan($player, $target);
            return;
        }

        if (GameShipEmergencyWarpServant::call($this->container, $target)->isSuccess) {
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

        if ($target->armor_pts < 1) {
            $this->targetDestroyed = true;
            $this->shipDestroy($player, $target);
        }

        if ($player->armor_pts < 1) {
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
        $attackShip->turn();

        ShipSaveServant::call($this->container, $attackShip);
        LogPlayerDAO::call($this->container, $underAttackShip->id, LogTypeConstants::LOG_ATTACK_OUTMAN, $attackShip->name);

        $this->mt('l_att_flee');
    }

    protected function attackOutscan(Ship $attackShip, Ship $underAttackShip): void
    {
        $attackShip->turn();

        ShipSaveServant::call($this->container, $attackShip);
        LogPlayerDAO::call($this->container, $underAttackShip->id, LogTypeConstants::LOG_ATTACK_OUTSCAN, $attackShip->name);

        $this->mt('l_planet_noscan');
    }

    protected function emergencyWarp(Ship $attackShip, Ship $underAttackShip): void
    {
        $attackShip->turn();

        ShipSaveServant::call($this->container, $attackShip);
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

        $attackShip->ore += $salv->salvOre;
        $attackShip->organics += $salv->salvOrganics;
        $attackShip->goods += $salv->salvGoods;
        $attackShip->credits += $salv->salvCredits;
        $attackShip->turn();

        ShipSaveServant::call($this->container, $attackShip);

        $this->mt([$attackShip->name, $loss->armorPts, 'l_armorpts', $loss->fighters, ',', 'l_fighters', 'l_att_andused', $loss->torps, 'l_torps']);
    }

    protected function shipDestroy(Ship $attackShip, Ship $destroyedShip): void
    {
        $this->mt([$destroyedShip->name, 'l_att_sdest']);

        if ($destroyedShip->dev_escapepod) {
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

        $targetLoss = $underAttackShip->battleState()->losses();

        $attackShip->turn();

        ShipSaveServant::call($this->container, $underAttackShip);
        ShipSaveServant::call($this->container, $attackShip);
        //
        LogPlayerDAO::call($this->container, $underAttackShip->id, LogTypeConstants::LOG_ATTACKED_WIN, [$attackShip->name, $targetLoss->armorPts, $targetLoss->fighters]);

        $this->mt([$attackShip->name, $targetLoss->armorPts, 'l_armorpts', $targetLoss->fighters, 'l_fighters', ',', 'l_att_andused', $targetLoss->torps, 'l_torps']);
    }
}
