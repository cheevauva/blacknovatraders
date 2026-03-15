<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

/**
 * Adding ore to all commodities ports
 * Adding ore to all ore ports
 * Ensuring minimum ore levels are 0
 * Adding organics to all commodities ports
 * Adding organics to all organics ports
 * Ensuring minimum organics levels are 0
 * Adding goods to all commodities ports
 * Adding goods to all goods ports
 * Ensuring minimum goods levels are 0
 * Adding energy to all commodities ports
 * Adding energy to all energy ports
 * Ensuring minimum energy levels are 0
 */
class SectorPortsDAO extends \UUA\DAO
{

    public int $multiplier;

    #[\Override]
    public function serve(): void
    {


        global $organics_limit;
        global $ore_limit;
        global $ore_rate;
        global $goods_limit;
        global $goods_rate;
        global $energy_limit;
        global $organics_rate;
        global $energy_rate;

        $queries = [
            "UPDATE universe SET port_ore = port_ore + (:ore_rate * :multiplier) WHERE port_type='ore' AND port_ore < :ore_limit",
            "UPDATE universe SET port_ore = port_ore + (:ore_rate * :multiplier) WHERE port_type!='special' AND port_type!='none' AND port_ore < :ore_limit",
            "UPDATE universe SET port_ore = 0 WHERE port_ore < 0",
            "UPDATE universe SET port_organics = port_organics + (:organics_rate * :multiplier) WHERE port_type = 'organics' AND port_organics < :organics_limit",
            "UPDATE universe SET port_organics = port_organics + (:organics_rate * :multiplier) WHERE port_type != 'special' AND port_type != 'none' AND port_organics < :organics_limit",
            "UPDATE universe SET port_organics = 0 WHERE port_organics < 0",
            "UPDATE universe SET port_goods = port_goods + (:goods_rate * :multiplier) WHERE port_type = 'goods' AND port_goods < :goods_limit",
            "UPDATE universe SET port_goods = port_goods + (:goods_rate * :multiplier) WHERE port_type != 'special' AND port_type != 'none' AND port_goods < :goods_limit",
            "UPDATE universe SET port_goods = 0 WHERE port_goods < 0",
            "UPDATE universe SET port_energy = port_energy + (:energy_rate * :multiplier) WHERE port_type='energy' AND port_energy< :energy_limit",
            "UPDATE universe SET port_energy = port_energy + (:energy_rate * :multiplier) WHERE port_type!='special' AND port_type!='none' AND port_energy< :energy_limit",
            "UPDATE universe SET port_energy = 0 WHERE port_energy<0",
            "UPDATE universe SET port_energy = :energy_limit WHERE port_energy >  :energy_limit",
            "UPDATE universe SET port_goods = :goods_limit WHERE  port_goods >  :goods_limit",
            "UPDATE universe SET port_organics = :organics_limit WHERE port_organics >  :organics_limit",
            "UPDATE universe SET port_ore= :ore_limit WHERE port_ore >  :ore_limit",
        ];

        foreach ($queries as $query) {
            $this->db()->q($query, [
                'ore_rate' => $ore_rate,
                'ore_limit' => $ore_limit,
                'multiplier' => $this->multiplier,
                'organics_rate' => $organics_rate,
                'organics_limit' => $organics_limit,
                'goods_rate' => $goods_rate,
                'goods_limit' => $goods_limit,
                'energy_rate' => $energy_rate,
                'energy_limit' => $energy_limit,
            ]);
        }
    }
}
