<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use Psr\Container\ContainerInterface;
use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
use BNT\SectorDefence\DAO\SectorDefencesDeleteByCriteriaDAO;
use BNT\SectorDefence\DAO\SectorDefencesUpdateDAO;

class GameDestroyFightersServant extends \UUA\Servant
{

    public int $sector;
    public int $numFighters;

    #[\Override]
    public function serve(): void
    {
        $defences = SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->secDestDesttor,
            'defence_type' => 'F',
        ])->defences;

        foreach ($defences as $defence) {
            if ($this->numFighters <= 0) {
                break;
            }

            if ($defence['quantity'] > $this->numFighters) {
                $defence['quantity'] -= $this->numFighters;
                SectorDefencesUpdateDAO::call($this->container, [
                    'quantity' => $defence['quantity'],
                ], [
                    'defence_id' => $defence['defence_id'],
                ]);
                $this->numFighters = 0;
            } else {
                SectorDefencesDeleteByCriteriaDAO::call($this->container, [
                    'defence_id' => $defence['defence_id'],
                ]);

                $this->numFighters -= $defence['quantity'];
            }
        }
    }

    public static function call(ContainerInterface $container, int $sector, int $numFighters): self
    {
        $self = self::new($container);
        $self->sector = $sector;
        $self->numFighters = $numFighters;
        $self->serve();

        return $self;
    }
}
