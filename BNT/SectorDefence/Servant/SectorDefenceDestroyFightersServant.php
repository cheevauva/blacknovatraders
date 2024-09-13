<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Servant;

use BNT\ServantInterface;
use BNT\SectorDefence\SectorDefence;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\SectorDefence\DAO\SectorDefenceRemoveByCriteriaDAO;
use BNT\SectorDefence\DAO\SectorDefenceSaveDAO;
use BNT\SectorDefence\SectorDefenceTypeEnum;

class SectorDefenceDestroyFightersServant implements ServantInterface
{

    public int $sector;
    public int $fighters;
    public array $defencesForChange = [];
    public array $defencesForRemove = [];
    public bool $doIt = true;

    public function serve(): void
    {
        if (empty($this->fighters)) {
            return;
        }

        $retrieveDefences = new SectorDefenceRetrieveManyByCriteriaDAO;
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

                $this->defencesAsRemoved[] = $defence;
                $fighters -= $defence->quantity;
            }
        }
        
        $this->doIt();
    }

    protected function doIt(): void
    {
        if (!$this->doIt) {
            return;
        }

        foreach ($this->defencesForChange as $defenceForChange) {
            $defenceForChange = SectorDefence::as($defenceForChange);

            SectorDefenceSaveDAO::call($defenceForChange);
        }

        foreach ($this->defencesAsRemoved as $defenceForRemove) {
            $defenceForRemove = SectorDefence::as($defenceForRemove);

            $removeDefence = new SectorDefenceRemoveByCriteriaDAO;
            $removeDefence->defence_id = $defenceForRemove->id;
            $removeDefence->serve();
        }
    }

    public static function call(SectorDefence|int $sector, int $fighters): self
    {
        $self = new static;
        $self->sector = is_int($sector) ? $sector : $sector->sector_id;
        $self->fighters = $fighters;
        $self->serve();

        return $self;
    }

}
