<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

/**
 * @todo
 */
class ShipNewWishEscapePodDAO extends ShipDAO
{

    public int $shipId;

    public function serve(): void
    {
        $this->db()->executeQuery("UPDATE {$this->table()} SET hull=0, engines=0, power=0, computer=0,sensors=0, beams=0, torp_launchers=0, torps=0, armor=0, armor_pts=100, cloak=0, shields=0, sector=0, ship_ore=0, ship_organics=0, ship_energy=1000, ship_colonists=0, ship_goods=0, ship_fighters=100, ship_damage=0, on_planet='N', dev_warpedit=0, dev_genesis=0, dev_beacon=0, dev_emerwarp=0, dev_escapepod='N', dev_fuelscoop='N', dev_minedeflector=0, ship_destroyed='N',dev_lssd='N' WHERE ship_id = :ship_id", [
            'ship_id' => $this->shipId,
        ]);
    }

}
