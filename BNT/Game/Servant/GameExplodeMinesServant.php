<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use Psr\Container\ContainerInterface;
use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
use BNT\SectorDefence\DAO\SectorDefencesDeleteByCriteriaDAO;
use BNT\SectorDefence\DAO\SectorDefencesUpdateDAO;

class GameExplodeMinesServant extends \UUA\Servant
{

    public int $sector;
    public int $numMines;

    #[\Override]
    public function serve(): void
    {
        $defences = SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
            'defence_type' => 'M',
        ])->defences;

        //Put the defence information into the array "defenceinfo"
        foreach ($defences as $defence) {
            if ($this->numMines <= 0) {
                break;
            }

            if ($defence['quantity'] > $this->numMines) {
                $defence['quantity'] -= $this->numMines;

                SectorDefencesUpdateDAO::call($this->container, $defence, [
                    'defence_id' => $defence['defence_id'],
                ]);
                $this->numMines = 0;
            } else {
                SectorDefencesDeleteByCriteriaDAO::call($this->container, [
                    'defence_id' => $defence['defence_id'],
                ]);

                $this->numMines -= $defence['quantity'];
            }
        }
    }

    public static function call(ContainerInterface $container, int $sector, int $numMines): self
    {
        $self = self::new($container);
        $self->sector = $sector;
        $self->numMines = $numMines;
        $self->serve();

        return $self;
    }
}
