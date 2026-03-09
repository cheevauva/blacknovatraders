<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Exception\WarningException;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Bounty\DAO\BountiesByCriteriaDAO;

class ScanController extends BaseController
{

    public ?array $targetinfo = null;
    public $success;
    public $sc_error;
    public $roll;
    public $btyamount;
    public $btyamountfed;
    public bool $isfedbounty = false;

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->t('l_scan_title');
        $this->checkTurns();
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        global $bounty_ratio;
        global $bounty_minturns;

        $this->targetinfo = ShipByIdDAO::call($this->container, $this->fromQueryParams('ship_id')->notEmpty()->asInt())->ship ?: throw new WarningException('l_not_found');

        /* check to ensure target is in the same sector as player */
        if ($this->targetinfo['sector'] != $this->playerinfo['sector']) {
            throw new WarningException('l_planet_noscan');
        }

        /* determine per cent chance of success in scanning target ship - based on player's sensors and opponent's cloak */
        
        $this->roll = rand(1, 100);
        $this->sc_error = SCAN_ERROR($this->playerinfo['sensors'], $this->targetinfo['cloak']);
        $this->success = SCAN_SUCCESS($this->playerinfo['sensors'], $this->targetinfo['cloak']);

        if ($this->success < 5) {
            $this->success = 5;
        }
        
        if ($this->success > 95) {
            $this->success = 95;
        }

        $this->playerinfo['turns'] -= 1;
        $this->playerinfo['turns_used'] += 1;
        $this->playerinfoUpdate();

        if ($this->roll > $this->success) {
            LogPlayerDAO::call($this->container, $this->targetinfo['ship_id'], LogTypeConstants::LOG_SHIP_SCAN_FAIL, $this->playerinfo['ship_name']);
            /* if scan fails - inform both player and target. */
            throw new WarningException('l_planet_noscan');
        }

        $playerscore = gen_score($this->playerinfo['ship_id']);
        $targetscore = gen_score($this->targetinfo['ship_id']);

        $playerscore *= $playerscore;
        $targetscore *= $targetscore;

        /* if scan succeeds, show results and inform target. */
        /* scramble results by scan error factor. */

        // Get total bounty on this player, if any
        $this->btyamount = array_sum(array_column(BountiesByCriteriaDAO::call($this->container, [
            'bounty_on' => $this->targetinfo['ship_id'],
        ])->bounties, 'amount'));

        // Check for Federation bounty
        $this->btyamountfed = array_sum(array_column(BountiesByCriteriaDAO::call($this->container, [
            'bounty_on' => $this->targetinfo['ship_id'],
            'placed_by' => 0,
        ])->bounties, 'amount'));

        $btyamount = $this->btyamountfed ?: $this->btyamount;

        // Player will get a Federation Bounty on themselves if they attack a player who's score is less than bounty_ratio of
        // themselves. If the target has a Federation Bounty, they can attack without attracting a bounty on themselves.
        $this->isfedbounty = $btyamount == 0 && ((($targetscore / $playerscore) < $bounty_ratio) || $this->targetinfo['turns_used'] < $bounty_minturns);

        LogPlayerDAO::call($this->container, $this->targetinfo['ship_id'], LogTypeConstants::LOG_SHIP_SCAN, $this->playerinfo['ship_name']);

        $this->render('tpls/scan.tpl.php');
    }
}
