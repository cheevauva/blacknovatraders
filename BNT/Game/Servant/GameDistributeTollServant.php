<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use Psr\Container\ContainerInterface;
use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;

class GameDistributeTollServant extends \UUA\Servant
{

    public int $sector;
    public int $totalFighters;
    public int $toll;

    #[\Override]
    public function serve(): void
    {
        $defences = SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
            'defence_type' => 'F',
        ])->defences;

        foreach ($defences as $defence) {
            $tollAmount = round(($defence['quantity'] / $this->totalFighters) * $this->toll);

            $ship = ShipByIdDAO::call($this->container, $defence['ship_id'])->ship;
            $ship['credits'] += $tollAmount;

            ShipUpdateDAO::call($this->container, $ship, $ship['ship_id']);
            LogPlayerDAO::call($this->container, $defence['ship_id'], LogTypeConstants::LOG_TOLL_RECV, [$tollAmount, $this->sector]);
        }
    }

    public static function call(ContainerInterface $container, int $sector, int $totalFighters, int $toll): self
    {
        $self = self::new($container);
        $self->sector = $sector;
        $self->toll = $toll;
        $self->totalFighters = $totalFighters;
        $self->serve();

        return $self;
    }
}
