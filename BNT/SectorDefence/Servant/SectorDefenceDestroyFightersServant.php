<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\Servant;
use BNT\SectorDefence\Entity\SectorDefence;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\SectorDefence\DAO\SectorDefenceRemoveByCriteriaDAO;
use BNT\SectorDefence\DAO\SectorDefenceSaveDAO;
use BNT\SectorDefence\SectorDefenceTypeEnum;


class SectorDefenceDestroyFightersServant extends Servant
{

    
    public int $sector;
    public int $fighters;
    public array $defencesForChange = [];
    public array $defencesForRemove = [];

    public function serve(): void
    {
        if (empty($this->fighters)) {
            return;
        }

        $retrieveDefences = SectorDefenceRetrieveManyByCriteriaDAO::new($this->container);
        $retrieveDefences->sector_id = $this->sector;
        $retrieveDefences->defence_type = SectorDefenceTypeEnum::Fighters;
        $retrieveDefences->orderByQuantityDESC = true;
        $retrieveDefences->serve();

        $fighters = $this->fighters;

        foreach ($retrieveDefences->defences as $defence) {
            $defence = SectorDefence::as($defence);

            if ($fighters < 1) {
                return;
            }

            if ($defence->quantity > $this->fighters) {
                $defence->quantity -= $this->fighters;

                $this->defencesForChange[] = $defence;
                $fighters = 0;
            } else {

                $this->defencesForRemove[] = $defence;
                $fighters -= $defence->quantity;
            }
        }


        foreach ($this->defencesForChange as $defenceForChange) {
            $defenceForChange = SectorDefence::as($defenceForChange);

            SectorDefenceSaveDAO::call($this->container, $defenceForChange);
        }

        foreach ($this->defencesAsRemoved as $defenceForRemove) {
            $defenceForRemove = SectorDefence::as($defenceForRemove);

            $removeDefence = SectorDefenceRemoveByCriteriaDAO::new($this->container);
            $removeDefence->defence_id = $defenceForRemove->id;
            $removeDefence->serve();
        }
    }

    public static function call(\Psr\Container\ContainerInterface $container, SectorDefence|int $sector, int $fighters): self
    {
        $self = static::new($container);
        $self->sector = is_int($sector) ? $sector : $sector->sector_id;
        $self->fighters = $fighters;
        $self->serve();

        return $self;
    }
}
