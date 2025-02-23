<?php

declare(strict_types=1);

namespace BNT\Bounty\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipRetrieveByIdDAO;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Bounty\DAO\BountyRemoveByCriteriaDAO;
use BNT\Bounty\Bounty;
use BNT\Log\Event\LogBountyCancelledEvent;
use BNT\Bounty\DAO\BountyRetrieveManyByCriteriaDAO;

class BountyCancelServant extends Servant
{

    public int $bounty_on;
    public bool $doIt = true;
    public array $logs = [];
    public array $shipsForChange = [];
    public array $bountiesForRemove = [];

    public function serve(): void
    {
        $retieveBounties = BountyRetrieveManyByCriteriaDAO::new($this->container);
        $retieveBounties->bounty_on = $this->bounty_on;
        $retieveBounties->serve();

        foreach ($retieveBounties->bounties as $bountydetails) {
            $bountydetails = Bounty::as($bountydetails);

            $bountyOn = ShipRetrieveByIdDAO::call($this->container, $bountydetails->bounty_on);

            if ($bountydetails->placed_by) {
                $placedBy = ShipRetrieveByIdDAO::call($this->container, $bountydetails->placed_by);
                $placedBy->credits += $bountydetails->amount;

                $this->shipsForChange[] = $placedBy;

                $log = new LogBountyCancelledEvent();
                $log->shipId = $bountydetails->placed_by;
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
            $removeBounty = BountyRemoveByCriteriaDAO::new($this->container);
            $removeBounty->bounty_id = Bounty::as($bountyForRemove)->bounty_id;
            $removeBounty->serve();
        }

        foreach ($this->shipsForChange as $ship) {
            ShipSaveDAO::call($this->container, $ship);
        }

        foreach ($this->logs as $log) {
            $log->dispatch($this->eventDispatcher());
        }
    }

    public static function call(\Psr\Container\ContainerInterface $container, Ship|int $bountyOn): void
    {
        if ($bountyOn instanceof Ship) {
            $bountyOn = $bountyOn->ship_id;
        }

        $self = static::new($container);
        $self->bounty_on = $bountyOn;
        $self->serve();
    }
}
