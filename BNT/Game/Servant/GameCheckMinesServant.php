<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
use BNT\Ship\DAO\ShipByIdDAO;

class GameCheckMinesServant extends \UUA\Servant
{

    use \BNT\Traits\OkTrait;

    public int $sector;
    public array $playerinfo;
    public protected(set) int $totalSectorMines;

    #[\Override]
    public function serve(): void
    {
        global $mine_hullsize;

        $defences = SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
            'defence_type' => 'M',
        ])->defences;

        // Correct the targetship bug to reflect the player info
        $targetship = $this->playerinfo;

        $numDefences = count($defences);
        $this->totalSectorMines = 0;
        $owner = true;

        foreach ($defences as $defence) {
            $this->totalSectorMines += $defence['quantity'];

            if ($defence['ship_id'] != $this->playerinfo['ship_id']) {
                $owner = false;
            }
        }

        // Compute the ship average...if its too low then the ship will not hit mines...
        $shipavg = shipScore($targetship);

        // The mines will attack if 4 conditions are met
        //    1) There is at least 1 group of mines in the sector
        //    2) There is at least 1 mine in the sector
        //    3) You are not the owner or on the team of the owner - team 0 dosent count
        //    4) You ship is at least $mine_hullsize (setable in config.php) big
        if (empty($numDefences) || empty($this->totalSectorMines) || $owner || $shipavg <= $mine_hullsize) {
            return;
        }

        // find out if the mine owner and player are on the same team
        $mineOwner = ShipByIdDAO::call($this->container, $defences[0]['ship_id'])->ship;

        if ($mineOwner['team'] != $this->playerinfo['team'] || empty($this->playerinfo['team'])) {
            $this->notOk();
        }
    }
}
