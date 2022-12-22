<?php

declare(strict_types=1);

namespace BNT\Planet\Mapper;

use BNT\Planet\Planet;
use BNT\ServantInterface;

class PlanetMapper implements ServantInterface
{

    public array $row;
    public Planet $planet;

    public function serve(): void
    {
        if (empty($this->planet) && !empty($this->row)) {
            $planet = $this->planet = new Planet;
            $planet->planet_id = intval($this->row['planet_id']);
        }

        if (!empty($this->planet) && empty($this->row)) {
            $planet = $this->planet;
            $row = [];
            $row['planet_id'] = $planet->planet_id;

            $this->row = $row;
        }
    }

}
