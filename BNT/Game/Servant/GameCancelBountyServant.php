<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Log\DAO\LogPlayerDAO;
use BNT\Log\LogTypeConstants;
use BNT\Bounty\DAO\BountiesByCriteriaDAO;
use BNT\Bounty\DAO\BountyDeleteByCriteriaDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Ship\DAO\ShipUpdateDAO;

class GameCancelBountyServant extends \UUA\Servant
{

    public int $ship;

    #[\Override]
    public function serve(): void
    {
        $bounties = BountiesByCriteriaDAO::call($this->container, [
            'bounty_on' => $this->ship,
        ])->bounties;
        
        $ship = ShipByIdDAO::call($this->container, $this->ship)->ship;

        foreach ($bounties as $bounty) {
            if (!empty($bounty['placed_by'])) {
                $placedBy = ShipByIdDAO::call($this->container, $bounty['placed_by'])->ship;
                $placedBy['credits'] += $bounty['amount'];
                
                ShipUpdateDAO::call($this->container, $placedBy, $placedBy['ship_id']);

                LogPlayerDAO($this->container, $bounty['placed_by'], LogTypeConstants::LOG_BOUNTY_CANCELLED, [
                    $bounty['amount'],
                    $ship['ship_name']
                ]);
            }

            BountyDeleteByCriteriaDAO::call($this->container, [
                'bounty_id' => $bounty['bounty_id'],
            ]);
        }
    }
}
