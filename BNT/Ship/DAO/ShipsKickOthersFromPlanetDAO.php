<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipsKickOthersFromPlanetDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $ship;
    public int $planet;

    #[\Override]
    public function serve(): void
    {
        // Kick other players off the planet
        $this->db()->q("UPDATE ships SET on_planet = 'N' WHERE on_planet = 'Y' AND planet_id = :planet_id AND ship_id != :ship_id", [
            'planet_id' => $this->planet,
            'ship_id' => $this->ship,
        ]);
    }
}
