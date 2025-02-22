<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Servant;
use BNT\Ship\Entity\Ship;
use BNT\Ship\DAO\ShipCreateDAO;
use BNT\Enum\BalanceEnum;

class ShipCreateServant extends Servant
{
    public Ship $ship;

    public function serve(): void
    {
        $ship = $this->ship;
        $ship->armor_pts = BalanceEnum::start_armor->val();
        $ship->credits = BalanceEnum::start_credits->val();
        $ship->turns = BalanceEnum::start_turns->val();
        $ship->ship_energy = BalanceEnum::start_energy->val();
        $ship->ship_fighters = BalanceEnum::start_fighters->val();
        $ship->dev_warpedit = BalanceEnum::start_editors->val();
        $ship->dev_genesis = BalanceEnum::start_genesis->val();
        $ship->dev_beacon = BalanceEnum::start_beacon->val();
        $ship->dev_emerwarp = BalanceEnum::start_emerwarp->val();
        $ship->dev_minedeflector = BalanceEnum::start_minedeflectors->val();
        $ship->dev_lssd = BalanceEnum::start_lssd->val();
        $ship->trade_colonists = true;
        $ship->trade_fighters = false;
        $ship->trade_torps = false;
        $ship->trade_energy = true;
        $ship->cleared_defences = null;
        $ship->lang = BalanceEnum::default_lang->val();
        $ship->dhtml = true;

        $create = ShipCreateDAO::new($this->container);
        $create->ship = $ship;
        $create->serve();
    }
}
