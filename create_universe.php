<?php

$disableAutoLogin = true;

include 'config.php';

$title = "Create Universe";

$step = fromPost('step');

if ($adminpass != fromPost('swordfish')) {
    $step = 0;
}

$sector_max = round(fromPost('sector_max', $sector_max));
$initscommod = fromPost('initscommod', 100);
$initbcommod = fromPost('initbcommod', 100);
$universe_size = fromPost('universe_size', $universe_size);
$special = fromPost('special', 1);
$ore = fromPost('ore', 15);
$organics = fromPost('organics', 10);
$goods = fromPost('goods', 15);
$energy = fromPost('energy', 10);
$planets = fromPost('planets', 10);
$fedsecs = fromPost('fedsecs', intval($sector_max / 200));
$initsore = $ore_limit * $initscommod / 100.0;
$initsorganics = $organics_limit * $initscommod / 100.0;
$initsgoods = $goods_limit * $initscommod / 100.0;
$initsenergy = $energy_limit * $initscommod / 100.0;
$initbore = $ore_limit * $initbcommod / 100.0;
$initborganics = $organics_limit * $initbcommod / 100.0;
$initbgoods = $goods_limit * $initbcommod / 100.0;
$initbenergy = $energy_limit * $initbcommod / 100.0;
$specialSectorsCount = round($sector_max * $special / 100);
$oreSectorsCount = round($sector_max * $ore / 100);
$organicsSectorsCount = round($sector_max * $organics / 100);
$goodsSectorsCount = round($sector_max * $goods / 100);
$energySectorsCount = round($sector_max * $energy / 100);
$fedSectorsCount = round($sector_max * $fedsecs / 100);
$empty = $sector_max - $specialSectorsCount - $oreSectorsCount - $organicsSectorsCount - $goodsSectorsCount - $energySectorsCount;
$unownedPlanetsCount = round($sector_max * $planets / 100);

switch ($step) {
    case 1:
        include 'tpls/create_universe/create_universe_step1.tpl.php';
        return;
    case 2:
        configUpdate([
            'sector_max' => $sector_max,
            'universe_size' => $universe_size,
        ]);
        include 'tpls/create_universe/create_universe_step2.tpl.php';
        return;
    case 3:
        $sql = "
        INSERT INTO universe (sector_id, zone_id, angle1, angle2, distance) 
        SELECT 
            null,
            1,
            FLOOR(RAND() * 180) AS angle1,
            FLOOR(RAND() * 90) AS angle2,
            ROUND(RAND() * :universe_size) AS distance
        FROM 
            information_schema.tables t1
        CROSS JOIN 
            information_schema.tables t2
        CROSS JOIN 
            information_schema.tables t3
        LIMIT :sector_max
        ";

        db()->q($sql, [
            'sector_max' => (int) $sector_max,
            'universe_size' => $universe_size,
        ], [
            'sector_max' => \PDO::PARAM_INT,
        ]);

        db()->q("UPDATE zones SET max_hull = :fed_max_hull WHERE zone_id = 2", [
            'fed_max_hull' => $fed_max_hull,
        ]);
        db()->q("UPDATE universe SET zone_id= 2 WHERE sector_id < :fedsecs", [
            'fedsecs' => $fedSectorsCount,
        ]);

        $specialPortsSql = "
        UPDATE 
            universe 
        SET 
            zone_id = 3,
            port_type = 'special' 
        WHERE 
            port_type = 'none' AND 
            sector_id IN (
                SELECT 
                    s.sector_id 
                FROM 
                    (
                        SELECT 
                            sector_id 
                        FROM 
                            universe 
                        WHERE 
                            port_type = 'none' 
                        ORDER BY 
                            rand() DESC 
                        LIMIT :spp
                    ) AS s
            )
        ";

        db()->q($specialPortsSql, [
            'spp' => (int) $specialSectorsCount,
        ], [
            'spp' => \PDO::PARAM_INT,
        ]);

        $portTypes = [
            'ore' => $oreSectorsCount,
            'organics' => $organicsSectorsCount,
            'goods' => $goodsSectorsCount,
            'energy' => $energySectorsCount,
        ];

        foreach ($portTypes as $portType => $limit) {
            $sql = "
            UPDATE 
                universe 
            SET 
                port_ore = :initsore,
                port_organics = :initborganics,
                port_goods = :initbgoods,
                port_energy = :initbenergy,
                port_type = :portType
            WHERE 
                port_type = 'none' AND 
                sector_id IN (
                    SELECT 
                        s.sector_id 
                    FROM 
                        (
                            SELECT 
                                sector_id 
                            FROM 
                                universe 
                            WHERE 
                                port_type = 'none' 
                            ORDER BY 
                                rand() DESC 
                            LIMIT :limit
                        ) AS s
                )
            ";
            db()->q($sql, [
                'limit' => (int) $limit,
                'portType' => $portType,
                'initsore' => $initsore,
                'initborganics' => $initborganics,
                'initbgoods' => $initbgoods,
                'initbenergy' => $initbenergy,
            ], [
                'limit' => \PDO::PARAM_INT,
            ]);
        }

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

        db()->q($planetsSql, [
            'default_prod_ore' => $default_prod_ore,
            'default_prod_organics' => $default_prod_organics,
            'default_prod_goods' => $default_prod_goods,
            'default_prod_energy' => $default_prod_energy,
            'default_prod_fighters' => $default_prod_fighters,
            'default_prod_torp' => $default_prod_torp,
            'nump' => (int) $unownedPlanetsCount,
        ], [
            'nump' => \PDO::PARAM_INT,
        ]);

        $linksTwoWaySql = "
        INSERT INTO links (link_start, link_dest, link_type)
        SELECT 
            @k := @i AS link_start,
            @i := @i + 1 AS link_dest,
            2 AS link_type
        FROM 
            (SELECT @i := 0) AS r,
            information_schema.tables t1
        CROSS JOIN 
            information_schema.tables t2
        CROSS JOIN 
            information_schema.tables t3
        LIMIT :sector_max
        ";

        db()->q($linksTwoWaySql, [
            'sector_max' => (int) $sector_max,
        ], [
            'sector_max' => \PDO::PARAM_INT,
        ]);

        $linksTwoWayRandonSql = "
        INSERT INTO links (link_start, link_dest, link_type)
        SELECT 
            ROUND(RAND() * :sector_max + 1) - 1 AS link_start,
            ROUND(RAND() * :sector_max + 1) - 1 AS link_dest,
            2 AS link_type
        FROM 
            information_schema.tables t1
        CROSS JOIN 
            information_schema.tables t2
        CROSS JOIN 
            information_schema.tables t3
        LIMIT :sector_max
        ";

        db()->q($linksTwoWayRandonSql, [
            'sector_max' => (int) $sector_max,
        ], [
            'sector_max' => \PDO::PARAM_INT,
        ]);

        $linksOneWaySql = "
        INSERT INTO links (link_start, link_dest, link_type)
        SELECT 
            ROUND(RAND() * :sector_max + 1) - 1 AS link_start,
            ROUND(RAND() * :sector_max + 1) - 1 AS link_dest,
            1 AS link_type
        FROM 
            information_schema.tables t1
        CROSS JOIN 
            information_schema.tables t2
        CROSS JOIN 
            information_schema.tables t3
        LIMIT :sector_max
        ";

        db()->q($linksOneWaySql, [
            'sector_max' => (int) $sector_max,
        ], [
            'sector_max' => \PDO::PARAM_INT,
        ]);

        $linksTwoWayBackSql = "
        INSERT INTO links (link_start, link_dest, link_type)
        SELECT
            links.link_dest AS link_start,
            links.link_start AS link_dest,
            links.link_type
        FROM
            links
        WHERE
            links.link_type = 2
        ";
        db()->q($linksTwoWayBackSql);

        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_turns.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_defenses.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_xenobe.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_igb, 0, 'sched_igb.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_news, 0, 'sched_news.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_planets, 0, 'sched_planets.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_ports, 0, 'sched_ports.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_turns, 0, 'sched_tow.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_ranking, 0, 'sched_ranking.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_degrade, 0, 'sched_degrade.php', NULL,unix_timestamp(now()))");
        $db->adoExecute("INSERT INTO scheduler VALUES(NULL, 'Y', 0, $sched_apocalypse, 0, 'sched_apocalypse.php', NULL,unix_timestamp(now()))");

        $shipAdminId = shipCreate([
            'ship_name' => 'WebMaster',
            'character_name' => 'WebMaster',
            'password' => md5($admin_pass),
            'email' => $admin_mail,
            'armor_pts' => $start_armor,
            'credits' => $start_credits,
            'ship_energy' => $start_energy,
            'ship_fighters' => $start_fighters,
            'turns' => $start_turns,
            'last_login' => date('Y-m-d H:i:s'),
            'lang' => $language,
            'role' => 'admin'
        ]);

        zoneCreate([
            'zone_name' => 'WebMaster\'s Territory',
            'owner' => $shipAdminId,
        ]);
        bankAccountCreate([
            'ship_id' => $shipAdminId,
        ]);
        include 'tpls/create_universe/create_universe_step7.tpl.php';
        break;
    default:
        include 'tpls/create_universe/create_universe_login.tpl.php';
        break;
}


