<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Bounty\Servant\BountyCancelServant;
use BNT\Ship\Servant\ShipKillServant;
use BNT\Log\DAO\LogCreateDAO;


class ShipDestroyServant extends Servant
{

    
    public Ship $ship;
    public bool $doIt = true;
    public bool $shipDestroyed = false;
    public bool $shipHasEscapePod = false;
    public BountyCancelServant $cancelBounty;
    public ?ShipKillServant $shipKill = null;
    public array $logs = [];

    public function serve(): void
    {
        $this->shipHasEscapePod = $this->ship->dev_escapepod;

        if ($this->ship->dev_escapepod) {
            $this->shipDestroyed = false;
            $this->ship->resetWithEscapePod();
            $this->ship->rating = intval(round($this->ship->rating / 2));
            $this->cancelBounty();
        } else {
            $this->shipDestroyed = true;
            $this->cancelBounty();
            $this->shipKill();
        }

        $this->doIt();
    }

    private function shipKill(): void
    {
        $this->shipKill = ShipKillServant::new($this->container);
        $this->shipKill->ship = $this->ship;
        $this->shipKill->doIt = $this->doIt;
        $this->shipKill->serve();
    }

    private function cancelBounty(): void
    {
        $this->cancelBounty = BountyCancelServant::new($this->container);
        $this->cancelBounty->bounty_on = $this->ship->ship_id;
        $this->cancelBounty->doIt = $this->doIt;
        $this->cancelBounty->serve();
    }

    private function doIt(): void
    {
        if (!$this->doIt) {
            return;
        }

        ShipSaveDAO::call($this->container, $this->ship);

        foreach ($this->logs as $log) {
            LogCreateDAO::call($this->container, $log);
        }
    }

    public static function call(\Psr\Container\ContainerInterface $container, Ship $ship): void
    {
        $self = static::new($container);
        $self->ship = $ship;
        $self->serve();
    }
}
