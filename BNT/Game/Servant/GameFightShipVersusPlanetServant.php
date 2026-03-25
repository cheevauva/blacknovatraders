<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Ship\Ship;
use BNT\Planet\Planet;

class GameFightShipVersusPlanetServant extends \UUA\Servant
{

    public Ship $player;
    public Planet $planet;

    #[\Override]
    public function serve(): void
    {
        
    }
}
