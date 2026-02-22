<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Ship\DAO\ShipUpdateDAO;

class ShipRestoreFromEscapePodServant extends \UUA\Servant
{

    public array $ship;

    #[\Override]
    public function serve(): void
    {
        global $start_energy;

        $this->ship['hull'] = 0;
        $this->ship['engines'] = 0;
        $this->ship['power'] = 0;
        $this->ship['sensors'] = 0;
        $this->ship['computer'] = 0;
        $this->ship['beams'] = 0;
        $this->ship['torp_launchers'] = 0;
        $this->ship['torps'] = 0;
        $this->ship['armor'] = 0;
        $this->ship['armor_pts'] = 100;
        $this->ship['cloak'] = 0;
        $this->ship['shields'] = 0;
        $this->ship['sector'] = 0;
        $this->ship['ship_organics'] = 0;
        $this->ship['ship_ore'] = 0;
        $this->ship['ship_goods'] = 0;
        $this->ship['ship_energy'] = $start_energy;
        $this->ship['ship_colonists'] = 0;
        $this->ship['ship_fighters'] = 100;
        $this->ship['dev_warpedit'] = 0;
        $this->ship['dev_genesis'] = 0;
        $this->ship['dev_beacon'] = 0;
        $this->ship['dev_emerwarp'] = 0;
        $this->ship['dev_escapepod'] = 'N';
        $this->ship['dev_fuelscoop'] = 'N';
        $this->ship['dev_minedeflector'] = 0;
        $this->ship['ship_destroyed'] = 'N';
        $this->ship['on_planet'] = 'N';
        $this->ship['cleared_defences'] = '';
        $this->ship['dev_lssd'] = 'N';

        ShipUpdateDAO::call($this->container, $this->ship, $this->ship['ship_id']);
    }
}
