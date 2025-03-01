<?php

declare(strict_types=1);

namespace BNT\SectorDefence\Mapper;

use BNT\SectorDefence\Entity\SectorDefence;
use BNT\Ship\Entity\Ship;
use BNT\Math\DTO\MathDefenceDTO;
use BNT\SectorDefence\Enum\SectorDefenceTypeEnum;

class SectorDefenceToMathDefenceMapper extends \BNT\Mapper
{

    public Ship $ship;
    public Ship $defenceShip;
    public SectorDefence $sectorDefence;
    public MathDefenceDTO $mathDefence;

    public function serve(): void
    {
        $this->mathDefence->quantity = $this->sectorDefence->quantity;
        $this->mathDefence->isFighters = $this->sectorDefence->defence_type == SectorDefenceTypeEnum::Fighters;
        $this->mathDefence->isMines = $this->sectorDefence->defence_type == SectorDefenceTypeEnum::Mines;
        $this->mathDefence->isOwner = $this->defenceShip->ship_id === $this->ship->ship_id;
        $this->mathDefence->isOwnerTeam = !empty($this->ship->team) && $this->ship->team === $this->defenceShip->team;
    }

}
