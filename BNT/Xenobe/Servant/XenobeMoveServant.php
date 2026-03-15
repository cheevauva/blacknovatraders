<?php

declare(strict_types=1);

namespace BNT\Xenobe\Servant;

use BNT\Xenobe\XenobeConstants;
use BNT\Game\Servant\GameFindSectorForAttackServant;

class XenobeMoveServant extends \UUA\Servant
{

    use \BNT\Traits\PlayerinfoTrait;

    public protected(set) int $targetSector;

    #[\Override]
    public function serve(): void
    {
        $findSectorForAttack = GameFindSectorForAttackServant::new($this->container);
        $findSectorForAttack->playerinfo = $this->playerinfo;
        $findSectorForAttack->serve();

        $this->targetSector = $findSectorForAttack->sector;

        if (empty($this->targetSector)) {
            // We have no target link for some reason
            // Reset target link so it is not zero
            $this->targetSector = $this->playerinfo['sector'];
            return;
        }

        if (in_array($this->playerinfo['aggression'], [XenobeConstants::AGGRESSION_ATTACK_SOMETIMES, XenobeConstants::AGGRESSION_ATTACK_ALWAYS], true)) {
            // Attack sector defences
            XenobeToSecDefServant::call($this->container, $this->playerinfo, $this->targetSector);
            return;
        }

        $this->playerinfo['sector'] = $this->targetSector;
        $this->playerinfoTurn();
        $this->playerinfoUpdate();
    }
}
