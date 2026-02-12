<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

class PlanetsGenerateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public $unownedPlanetsCount;

    #[\Override]
    public function serve(): void
    {
        global $default_prod_torp;
        global $default_prod_fighters;
        global $default_prod_energy;
        global $default_prod_goods;
        global $default_prod_organics;
        global $default_prod_ore;

        $planetsSql = "
        INSERT INTO planets (colonists, owner, corp, prod_ore, prod_organics, prod_goods, prod_energy, prod_fighters, prod_torp, sector_id)
        SELECT 
            2 AS colonists,
            0 AS owner,
            0 AS corp,
            :default_prod_ore AS prod_ore,
            :default_prod_organics AS prod_organics,
            :default_prod_goods AS prod_goods,
            :default_prod_energy AS prod_energy, 
            :default_prod_fighters AS prod_fighters, 
            :default_prod_torp AS prod_torp,
            (SELECT universe.sector_id FROM universe, zones WHERE zones.zone_id = universe.zone_id AND zones.allow_planet = 'N' ORDER BY RAND() DESC LIMIT 1) AS sector_id
        FROM 
            information_schema.tables t1
        CROSS JOIN 
            information_schema.tables t2
        CROSS JOIN 
            information_schema.tables t3
        LIMIT :nump
        ";

        $this->db()->q($planetsSql, [
            'default_prod_ore' => $default_prod_ore,
            'default_prod_organics' => $default_prod_organics,
            'default_prod_goods' => $default_prod_goods,
            'default_prod_energy' => $default_prod_energy,
            'default_prod_fighters' => $default_prod_fighters,
            'default_prod_torp' => $default_prod_torp,
            'nump' => (int) $this->unownedPlanetsCount,
        ], [
            'nump' => \PDO::PARAM_INT,
        ]);
    }
}
