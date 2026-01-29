<?php

declare(strict_types=1);

namespace BNT\Math\Mediator;

use BNT\SectorDefence\DTO\SectorDefenceWithFightersOwnerDTO;
use BNT\Math\Calculator\DTO\MathCalcDefenceDTO;
use BNT\Math\Calculator\Servant\MathDefenceCalculateFightersServant;
use BNT\Ship\Mapper\ShipToMathShipMapper;
use BNT\Ship\Entity\Ship;

class MathDefenceCalculateFightersMediator extends \BNT\Mediator
{

    /**
     * @var SectorDefenceWithFightersOwnerDTO[]
     */
    public array $defences;
    public Ship $ship;
    public protected(set) int $totalFighters = 0;
    public protected(set) int $fightersToll = 0;
    public protected(set) bool $hasEmenyFighters = false;

    #[\Override]
    public function serve(): void
    {
        $mathCalcDefences = [];

        foreach ($this->defences as $defence) {
            $defence = SectorDefenceWithFightersOwnerDTO::as($defence);

            $mathCalcDefence = new MathCalcDefenceDTO();
            $mathCalcDefence->quantity = $defence->defence->quantity;
            $mathCalcDefence->isFighters = $defence->defence->isFighters();
            $mathCalcDefence->isMines = $defence->defence->isMines();
            $mathCalcDefence->isOwner = $defence->fightersOwner->isMe($this->ship);
            $mathCalcDefence->isOwnerTeam = $defence->fightersOwner->isMyTeam($this->ship);

            $mathCalcDefences[] = $mathCalcDefence;
        }

        $calculate = MathDefenceCalculateFightersServant::new($this->container);
        $calculate->ship = ShipToMathShipMapper::call($this->container, $this->ship)->mathShip;
        $calculate->defences = $mathCalcDefences;
        $calculate->serve();

        $this->totalFightes = $calculate->totalFighters;
        $this->fightersToll = $calculate->fightersToll;
        $this->hasEmenyFighters = $calculate->hasEmenyFighters;
    }
}
