<?php

declare(strict_types=1);

namespace BNT\Planet\Mapper;

use BNT\Planet\Entity\Planet;
use BNT\Mapper;

class PlanetMapper implements Mapper
{
    public array $row;
    public Planet $planet;

    public function serve(): void
    {
        if (empty($this->planet) && !empty($this->row)) {
            $planet = $this->planet = new Planet();
            $planet->planet_id = intval($this->row['planet_id']);
            $planet->sector_id = $this->row['sector_id'];
            $planet->name = $this->row['name'];
            $planet->organics = $this->row['organics'];
            $planet->ore = $this->row['ore'];
            $planet->goods = $this->row['goods'];
            $planet->energy = $this->row['energy'];
            $planet->colonists = $this->row['colonists'];
            $planet->credits = $this->row['credits'];
            $planet->fighters = $this->row['fighters'];
            $planet->torps = $this->row['torps'];
            $planet->owner = $this->row['owner'];
            $planet->corp = $this->row['corp'];
            $planet->base = toBool($this->row['base']);
            $planet->sells = toBool($this->row['sells']);
            $planet->prod_organics = $this->row['prod_organics'];
            $planet->prod_ore = $this->row['prod_ore'];
            $planet->prod_goods = $this->row['prod_goods'];
            $planet->prod_energy = $this->row['prod_energy'];
            $planet->prod_fighters = $this->row['prod_fighters'];
            $planet->prod_torp = $this->row['prod_torp'];
            $planet->defeated = toBool($this->row['defeated']);
        }

        if (!empty($this->planet) && empty($this->row)) {
            $planet = $this->planet;
            $this->row = [];
            $this->row['planet_id'] = $planet->planet_id;
            $this->row['sector_id'] = $planet->sector_id;
            $this->row['name'] = $planet->name;
            $this->row['organics'] = $planet->organics;
            $this->row['ore'] = $planet->ore;
            $this->row['goods'] = $planet->goods;
            $this->row['energy'] = $planet->energy;
            $this->row['colonists'] = $planet->colonists;
            $this->row['credits'] = $planet->credits;
            $this->row['fighters'] = $planet->fighters;
            $this->row['torps'] = $planet->torps;
            $this->row['owner'] = $planet->owner;
            $this->row['corp'] = $planet->corp;
            $this->row['base'] = fromBool($planet->base);
            $this->row['sells'] = fromBool($planet->sells);
            $this->row['prod_organics'] = $planet->prod_organics;
            $this->row['prod_ore'] = $planet->prod_ore;
            $this->row['prod_goods'] = $planet->prod_goods;
            $this->row['prod_energy'] = $planet->prod_energy;
            $this->row['prod_fighters'] = $planet->prod_fighters;
            $this->row['prod_torp'] = $planet->prod_torp;
            $this->row['defeated'] = fromBool($planet->defeated);
        }
    }
}
