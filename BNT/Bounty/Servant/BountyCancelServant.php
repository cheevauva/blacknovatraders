<?php

declare(strict_types=1);

namespace BNT\Bounty\Servant;

use BNT\ServantInterface;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Bounty\DAO\BountyRemoveByCriteriaDAO;
use BNT\Bounty\Bounty;
use BNT\Log\LogBountyCancelled;
use BNT\Log\DAO\LogCreateDAO;
use BNT\Bounty\DAO\BountyRetrieveManyByCriteriaDAO;

class BountyCancelServant implements ServantInterface
{
    public int $bounty_on;
    public bool $doIt = true;
    public array $logs = [];
    public array $shipsForChange = [];
    public array $bountiesForRemove = [];

    public function serve(): void
    {
        $retieveBounties = new BountyRetrieveManyByCriteriaDAO;
        $retieveBounties->bounty_on = $this->bounty_on;
        $retieveBounties->serve();

        foreach ($retieveBounties->bounties as $bountydetails) {
            $bountydetails = Bounty::as($bountydetails);

            $bountyOn = ShipRetrieveByIdDAO::call($bountydetails->bounty_on);

            if ($bountydetails->placed_by) {
                $placedBy = ShipRetrieveByIdDAO::call($bountydetails->placed_by);
                $placedBy->credits += $bountydetails->amount;

                $this->shipsForChange[] = $placedBy;

                $log = new LogBountyCancelled;
                $log->ship_id = $bountydetails->placed_by;
                $log->amount = $bountydetails->amount;
                $log->characterName = $bountyOn->character_name;

                $this->logs[] = $log;
            }

            $this->bountiesForRemove[] = $bountydetails;
        }

        $this->doIt();
    }

    private function doIt(): void
    {
        if (!$this->doIt) {
            return;
        }

        foreach ($this->bountiesForRemove as $bountyForRemove) {
            $removeBounty = new BountyRemoveByCriteriaDAO;
            $removeBounty->bounty_id = Bounty::as($bountyForRemove)->bounty_id;
            $removeBounty->serve();
        }

        foreach ($this->shipsForChange as $ship) {
            ShipSaveDAO::call($ship);
        }

        foreach ($this->logs as $log) {
            LogCreateDAO::call($log);
        }
    }

    public static function call(Ship|int $bountyOn): void
    {
        if ($bountyOn instanceof Ship) {
            $bountyOn = $bountyOn->ship_id;
        }

        $self = new static;
        $self->bounty_on = $bountyOn;
        $self->serve();
    }
}
