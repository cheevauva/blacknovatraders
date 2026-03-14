<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Game\Servant\GameSectorFightersServant;
use BNT\SectorDefence\DAO\SectorDefencesCleanUpDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Log\LogTypeConstants;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Translate;

class GameMoveServant extends \UUA\Servant
{

    use \BNT\Traits\TranslateTrait;

    public array $fightersOwner;
    public string $response;
    public int $sector;
    public array $playerinfo;
    public int $totalSectorFighters;
    public protected(set) array $messages = [];
    public protected(set) bool $ok = true;



    protected function playerinfoUpdate(): void
    {
        ShipUpdateDAO::call($this->container, $this->playerinfo, $this->playerinfo['ship_id']);
    }
}
