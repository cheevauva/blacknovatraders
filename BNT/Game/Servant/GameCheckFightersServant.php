<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
use BNT\Ship\DAO\ShipByIdDAO;

class GameCheckFightersServant extends \UUA\Servant
{

    public int $sector;
    public array $playerinfo;
    public protected(set) bool $ok = true;
    public protected(set) int $totalSectorFighters;
    public protected(set) ?array $fightersOwner = null;

    #[\Override]
    public function serve(): void
    {
        $defencesBySector = SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
            'defence_type' => 'F',
        ])->defences;

        $this->totalSectorFighters = 0;
        $owner = true;
        $fightersOwnerId = null;

        foreach ($defencesBySector as $defence) {
            $this->totalSectorFighters += $defence['quantity'];

            if ($defence['ship_id'] != $this->playerinfo['ship_id']) {
                $owner = false;
                $fightersOwnerId ??= $defence['ship_id'];
            }
        }

        $isProblem = !empty($defencesBySector) && !empty($this->totalSectorFighters) && !$owner;

        if (!$isProblem) {
            return;
        }

        // find out if the fighter owner and player are on the same team
        // All sector defences must be owned by members of the same team
        $this->fightersOwner = ShipByIdDAO::call($this->container, $fightersOwnerId)->ship;
        $isProblem2 = $this->fightersOwner['team'] != $this->playerinfo['team'] || empty($this->playerinfo['team']);

        if (!$isProblem2) {
            return;
        }

        $this->ok = false;
    }
}
