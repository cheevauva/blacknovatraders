<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\DAO\ShipsByCriteriaDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Bounty\DAO\BountiesByCriteriaDAO;
use BNT\Bounty\DAO\BountyByIdDAO;
use BNT\Bounty\DAO\BountyDeleteByCriteriaDAO;
use BNT\Bounty\DAO\BountyCreateDAO;
use BNT\Exception\WarningException;

class BountyController extends BaseController
{

    public array $bounty_on = [];
    public array $bounty_details = [];
    public array $bounties = [];
    public array $ships = [];
    public ?string $response = null;

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->l->by_title;
        $this->response = $this->fromQueryParams('response');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $bounty_maxvalue;
        
        $this->response = $this->fromParsedBody('response');
        
        if ($this->response === 'place') {
            $this->checkTurns();
            $bountyOn = (int) $this->fromParsedBody('bounty_on', 'bounty_on ' . $this->l->is_required);
            $amount = (int) $this->fromParsedBody('amount', 'amount ' . $this->l->is_required);
            $bountyOnShip = ShipByIdDAO::call($this->container, $bountyOn)->ship;

            if (!$bountyOnShip) {
                throw new WarningException($this->l->by_notexists);
            }

            $this->bounty_on = $bountyOnShip;

            if ($bountyOnShip['ship_destroyed'] == 'Y') {
                throw new WarningException($this->l->by_destroyed);
            }

            if ($bountyOn == $this->playerinfo['ship_id']) {
                throw new WarningException($this->l->by_yourself);
            }

            if (empty($amount)) {
                throw new WarningException($this->l->by_zeroamount);
            }

            if ($amount > $this->playerinfo['credits']) {
                throw new WarningException($this->l->by_notenough);
            }

            if ($bounty_maxvalue != 0) {
                $percent = $bounty_maxvalue * 100;
                $score = gen_score($this->playerinfo['ship_id']);
                $maxtrans = $score * $score * $bounty_maxvalue;

                $previous_bounty = 0;
                $prev = db()->fetch("SELECT SUM(amount) AS totalbounty FROM bounty WHERE bounty_on = :bounty_on AND placed_by = :placed_by", [
                    'bounty_on' => $bountyOn,
                    'placed_by' => $this->playerinfo['ship_id']
                ]);

                if ($prev) {
                    $previous_bounty = $prev['totalbounty'];
                }

                if ($amount + $previous_bounty > $maxtrans) {
                    throw new WarningException($this->l->by_toomuch);
                }
            }

            BountyCreateDAO::call($this->container, [
                'bounty_on' => $bountyOn,
                'placed_by' => $this->playerinfo['ship_id'],
                'amount' => $amount
            ]);

            $this->playerinfo['turns'] -= 1;
            $this->playerinfo['turns_used'] += 1;
            $this->playerinfo['credits'] -= $amount;
            $this->playerinfoUpdate();
            $this->redirectTo('bounty.php');
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        if (empty($this->response)) {
            $this->bounties = db()->fetchAll("SELECT bounty_on, SUM(amount) as total_bounty FROM bounty GROUP BY bounty_on");

            foreach ($this->bounties as $idxBounty => $bounty) {
                $this->bounties[$idxBounty]['target'] = ShipByIdDAO::call($this->container, $bounty['bounty_on'])->ship;
            }

            $this->ships = ShipsByCriteriaDAO::call($this->container, [
                'ship_destroyed' => 'N',
            ])->ships;

            foreach ($this->ships as $idxShip => $ship) {
                if ($this->playerinfo['ship_id'] == $ship['ship_id']) {
                    unset($this->ships[$idxShip]);
                }
            }
        }

        if ($this->response === 'cancel') {
            $this->checkTurns();
            $bountyId = (int) $this->fromQueryParams('bid', 'bid ' . $this->l->is_required);
            $bounty = BountyByIdDAO::call($this->container, $bountyId)->bounty;

            if (!$bounty) {
                throw new WarningException($this->l->by_nobounty);
            }

            if ($bounty['placed_by'] != $this->playerinfo['ship_id']) {
                throw new WarningException($this->l->by_notyours);
            }

            BountyDeleteByCriteriaDAO::call($this->container, [
                'bounty_id' => $bountyId,
            ]);

            $this->playerinfo['turns'] -= 1;
            $this->playerinfo['turns_used'] += 1;
            $this->playerinfo['credits'] += $bounty['amount'];
            $this->playerinfoUpdate();
            $this->redirectTo(sprintf('bounty.php?bounty_on=%s&response=display', $bounty['bounty_on']));
            return;
        }

        if ($this->response === 'display') {
            $bountyOn = (int) $this->fromQueryParams('bounty_on', 'bounty_on ' . $this->l->is_required);
            $this->bounty_on = ShipByIdDAO::call($this->container, $bountyOn)->ship;
            $this->bounty_details = BountiesByCriteriaDAO::call($this->container, [
                'bounty_on' => $this->fromQueryParams('bounty_on', 'bounty_on ' . $this->l->is_required),
            ])->bounties;

            foreach ($this->bounty_details as $idxBountyDtls => $bountyDetails) {
                if (empty($bountyDetails['placed_by'])) {
                    continue;
                }

                $this->bounty_details[$idxBountyDtls]['placer_info'] = ShipByIdDAO::call($this->container, $bountyDetails['placed_by'])->ship;
            }
        }

        $this->render('tpls/bounty.tpl.php');
    }
}
