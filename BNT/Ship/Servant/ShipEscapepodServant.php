<?php

//declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Ship\DAO\ShipUpdateDAO;

class ShipEscapepodServant extends \UUA\Servant
{

    public $ship;

    public function serve()
    {
        global $start_energy;

        $ship = $this->ship;
        $ship['hull'] = 0;
        $ship['engines'] = 0;
        $ship['power'] = 0;
        $ship['sensors'] = 0;
        $ship['computer'] = 0;
        $ship['beams'] = 0;
        $ship['torp_launchers'] = 0;
        $ship['torps'] = 0;
        $ship['armor'] = 0;
        $ship['armor_pts'] = 100;
        $ship['cloak'] = 0;
        $ship['shields'] = 0;
        $ship['sector'] = 0;
        $ship['ship_organics'] = 0;
        $ship['ship_ore'] = 0;
        $ship['ship_goods'] = 0;
        $ship['ship_energy'] = $start_energy;
        $ship['ship_colonists'] = 0;
        $ship['ship_fighters'] = 100;
        $ship['dev_warpedit'] = 0;
        $ship['dev_genesis'] = 0;
        $ship['dev_beacon'] = 0;
        $ship['dev_emerwarp'] = 0;
        $ship['dev_escapepod'] = 'N';
        $ship['dev_fuelscoop'] = 'N';
        $ship['dev_minedeflector'] = 0;
        $ship['on_planet'] = 'N';
        $ship['cleared_defences'] = '';
        $ship['dev_lssd'] = 'N';

        $update = ShipUpdateDAO::_new($this->container);
        $update->ship = $ship;
        $update->serve();
    }
}
