<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\DAO\ShipsByCriteriaDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Bounty\DAO\BountiesByCriteriaDAO;
use BNT\Bounty\DAO\BountyByIdDAO;
use BNT\Bounty\DAO\BountyDeleteByCriteriaDAO;
use BNT\Exception\WarningException;
use BNT\Bounty\Servant\BountyPlaceServant;

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
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        $this->checkTurns();

        $this->response = $this->fromParsedBody('response')->enum(['place', 'cancel'])->asString();

        if ($this->response === 'place') {
            $amount = $this->fromParsedBody('amount')->notEmpty()->asInt();
            $bountyOn = $this->fromParsedBody('bounty_on')->notEmpty()->asInt();

            $place = BountyPlaceServant::new($this->container);
            $place->placedBy = $this->playerinfo['ship_id'];
            $place->bountyOn = $bountyOn;
            $place->amount = $amount;
            $place->serve();

            $this->playerinfo['turns'] -= 1;
            $this->playerinfo['turns_used'] += 1;
            $this->playerinfo['credits'] -= $amount;
            $this->playerinfoUpdate();
            $this->redirectTo('bounty');
            return;
        }

        if ($this->response === 'cancel') {
            $bountyId = $this->fromParsedBody('bid')->notEmpty()->asInt();
            $bounty = BountyByIdDAO::call($this->container, $bountyId)->bounty;

            if (!$bounty) {
                throw new WarningException('l_by_nobounty');
            }

            if ($bounty['placed_by'] != $this->playerinfo['ship_id']) {
                throw new WarningException('l_by_notyours');
            }

            BountyDeleteByCriteriaDAO::call($this->container, [
                'bounty_id' => $bountyId,
            ]);

            $this->playerinfo['turns'] -= 1;
            $this->playerinfo['turns_used'] += 1;
            $this->playerinfo['credits'] += $bounty['amount'];
            $this->playerinfoUpdate();
            $this->redirectTo('bounty', [
                'bounty_on' => $bounty['bounty_on'],
                'response' => 'display',
            ]);
            return;
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->response = $this->fromQueryParams('response')->enum([null, 'display'])->asString();
        
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

        if ($this->response === 'display') {
            $bountyOn = $this->fromQueryParams('bounty_on')->notEmpty()->asInt();
            $this->bounty_on = ShipByIdDAO::call($this->container, $bountyOn)->ship;
            $this->bounty_details = BountiesByCriteriaDAO::call($this->container, [
                'bounty_on' => $this->fromQueryParams('bounty_on')->notEmpty()->asInt(),
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
