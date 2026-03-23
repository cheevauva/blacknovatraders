<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use Psr\Container\ContainerInterface;

class GameShipEmergencyWarpServant extends \UUA\Servant
{

    public Ship $ship;
    public protected(set) bool $isSuccess = false;

    #[\Override]
    public function serve(): void
    {
        global $ewd_maxhullsize;
        global $sector_max;

        $shipScore = $this->ship->score();

        if ($shipScore > $ewd_maxhullsize) {
            $chance = ($shipScore - $ewd_maxhullsize) * 10;
        } else {
            $chance = 0;
        }

        if (empty($this->ship->dev_emerwarp) && $this->randomValue() < $chance) {
            return;
        }

        $destSector = rand(1, $sector_max);

        $this->ship->sector = $destSector;
        $this->ship->dev_emerwarp -= 1;
        $this->ship->cleared_defences = '';

        ShipSaveServant::call($this->container, $this->ship);
        MovementLogDAO::call($this->container, $this->ship->id, $destSector);

        $this->isSuccess = true;
    }

    protected function randomValue(): int
    {
        return rand(1, 100);
    }

    public static function call(ContainerInterface $container, int $ship): self
    {
        $self = self::new($container);
        $self->ship = $ship;
        $self->serve();

        return $self;
    }
}
