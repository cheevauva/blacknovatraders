<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\ServantInterface;
use BNT\Ship\Ship;
use BNT\Ship\DAO\ShipCreateDAO;

class ShipCreateServant implements ServantInterface
{

    public Ship $ship;

    public function serve(): void
    {
        global $start_armor;
        global $start_credits;
        global $start_energy;
        global $start_fighters;
        global $start_turns;
        global $start_editors;
        global $start_genesis;
        global $start_beacon;
        global $start_emerwarp;
        global $start_minedeflectors;
        global $start_lssd;
        global $default_lang;

        $ship = $this->ship;
        $ship->armor_pts = $start_armor;
        $ship->credits = $start_credits;
        $ship->turns = $start_turns;
        $ship->ship_energy = $start_energy;
        $ship->ship_fighters = $start_fighters;
        $ship->dev_warpedit = $start_editors;
        $ship->dev_genesis = $start_genesis;
        $ship->dev_beacon = $start_beacon;
        $ship->dev_emerwarp = $start_emerwarp;
        $ship->dev_minedeflector = $start_minedeflectors;
        $ship->dev_lssd = $start_lssd;
        $ship->trade_colonists = true;
        $ship->trade_fighters = false;
        $ship->trade_torps = false;
        $ship->trade_energy = true;
        $ship->cleared_defences = null;
        $ship->lang = $default_lang;
        $ship->dhtml = true;

        $create = new ShipCreateDAO;
        $create->ship = $ship;
        $create->serve();
    }

}
