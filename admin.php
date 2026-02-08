<?php

include 'config.php';

function CHECKED($yesno)
{
    return(($yesno == 'Y') ? "CHECKED" : "");
}

function YESNO($onoff)
{
    return(($onoff == "ON") ? 'Y' : 'N');
}

try {
    $module = fromGet('module');
    $operation = fromGet('operation');

    if ($playerinfo['role'] !== 'admin') {
        throw new \Exception('You not admin');
    }

    switch (requestMethod()) {
        case 'POST':
            if ($module === 'sectoredit' && $operation === 'save') {
                $sector = (int) fromGET('sector', new \Exception('sector'));

                sectorUpdate($sector, [
                    'sector_name' => (string) fromPost('sector_name'),
                    'zone_id' => (int) fromPost('zone_id'),
                    'beacon' => (string) fromPost('beacon'),
                    'port_type' => (string) fromPost('port_type'),
                    'port_organics' => (int) fromPost('port_organics'),
                    'port_ore' => (int) fromPost('port_ore'),
                    'port_goods' => (int) fromPost('port_goods'),
                    'port_energy' => (int) fromPost('port_energy'),
                    'distance' => (int) fromPost('distance'),
                    'angle1' => (float) fromPost('angle1'),
                    'angle2' => (float) fromPost('angle2'),
                ]);
            }

            if ($module === 'univedit' && $operation === 'doexpand') {
                $radius = (int) fromPost('radius', new \Exception('radius'));
                configUpdate([
                    'universe_size' => $universe_size,
                ]);

                db()->q('UPDATE universe SET distance = FLOOR(RAND() * :radius) WHERE 1 = 1', [
                    'radius' => $radius + 1,
                ]);
            }

            if ($module === 'useredit' && $operation === 'save') {
                $user = (int) fromGet('user', new \Exception('user'));
                $row = BNT\ShipFunc::shipById($user);

                BNT\ShipFunc::shipUpdate($user, [
                    'character_name' => (string) fromPost('character_name', new \Exception('character_name')),
                    'password' => fromPost('password2') ? md5((string) fromPost('password2')) : $row['password'],
                    'email' => (string) fromPost('email', new \Exception('email')),
                    'ship_name' => (string) fromPost('ship_name', new \Exception('ship_name')),
                    'ship_destroyed' => !fromPost('ship_destroyed') ? 'N' : 'Y',
                    'hull' => (int) fromPost('hull', 0),
                    'engines' => (int) fromPost('engines', 0),
                    'power' => (int) fromPost('power', 0),
                    'computer' => (int) fromPost('computer', 0),
                    'sensors' => (int) fromPost('sensors', 0),
                    'armor' => (int) fromPost('armor', 0),
                    'shields' => (int) fromPost('shields', 0),
                    'beams' => (int) fromPost('beams', 0),
                    'torp_launchers' => (int) fromPost('torp_launchers', 0),
                    'cloak' => (int) fromPost('cloak', 0),
                    'credits' => (int) fromPost('credits', 0),
                    'turns' => (int) fromPost('turns', 0),
                    'dev_warpedit' => (int) fromPost('dev_warpedit'),
                    'dev_genesis' => (int) fromPost('dev_genesis'),
                    'dev_beacon' => (int) fromPost('dev_beacon'),
                    'dev_emerwarp' => (int) fromPost('dev_emerwarp'),
                    'dev_escapepod' => !fromPost('dev_escapepod') ? 'N' : 'Y',
                    'dev_fuelscoop' => !fromPost('dev_fuelscoop') ? 'N' : 'Y',
                    'dev_minedeflector' => (int) fromPost('dev_minedeflector'),
                    'sector' => (int) fromPost('sector'),
                    'ship_ore' => (int) fromPost('ship_ore'),
                    'ship_organics' => (int) fromPost('ship_organics'),
                    'ship_goods' => (int) fromPost('ship_goods'),
                    'ship_energy' => (int) fromPost('ship_energy'),
                    'ship_colonists' => (int) fromPost('ship_colonists'),
                    'ship_fighters' => (int) fromPost('ship_fighters'),
                    'torps' => (int) fromPost('torps'),
                    'armor_pts' => (int) fromPost('armor_pts'),
                ]);
            }

            if ($module === 'planedit' && $operation === 'save') {
                $planet = (int) fromGet('planet', new \Exception('planet'));

                planetUpdate($planet, [
                    'sector_id' => (int) fromPost('sector_id'),
                    'defeated' => !fromPost('defeated') ? 'N' : 'Y',
                    'name' => (string) fromPost('name'),
                    'base' => !fromPost('base') ? 'N' : 'Y',
                    'sells' => !fromPost('sells') ? 'N' : 'Y',
                    'owner' => (string) fromPost('owner'),
                    'organics' => (int) fromPost('organics', 0),
                    'ore' => (int) fromPost('ore', 0),
                    'goods' => (int) fromPost('goods', 0),
                    'energy' => (int) fromPost('energy', 0),
                    'corp' => (string) fromPost('corp'),
                    'colonists' => (int) fromPost('colonists', 0),
                    'credits' => (int) fromPost('credits', 0),
                    'fighters' => (int) fromPost('fighters', 0),
                    'torps' => (int) fromPost('torps', 0),
                    'prod_organics' => (int) fromPost('prod_organics', 0),
                    'prod_ore' => (int) fromPost('prod_ore', 0),
                    'prod_goods' => (int) fromPost('prod_goods', 0),
                    'prod_energy' => (int) fromPost('prod_energy', 0),
                    'prod_fighters' => (int) fromPost('prod_fighters', 0),
                    'prod_torp' => (int) fromPost('prod_torp', 0)
                ]);
            }

            if ($module === 'zoneedit' && $operation === 'save') {
                $zone = (int) fromGet('zone', new \Exception('zone'));

                zoneUpdate($zone, [
                    'zone_name' => (string) fromPost('zone_name'),
                    'allow_beacon' => !fromPost('zone_beacon') ? 'N' : 'Y',
                    'allow_attack' => !fromPost('zone_attack') ? 'N' : 'Y',
                    'allow_warpedit' => !fromPost('zone_warpedit') ? 'N' : 'Y',
                    'allow_planet' => !fromPost('zone_planet') ? 'N' : 'Y',
                    'max_hull' => (int) fromPost('zone_hull', 0)
                ]);
            }

            redirectTo('admin.php');
            break;
        case 'GET':
            if ($module === 'sectoredit' && $operation === 'edit') {
                $sector = (int) fromGet('sector', new \Exception('sector'));

                $row = sectoryById($sector);
                $zones = db()->fetchAllKeyValue('SELECT zone_id, zone_name FROM zones ORDER BY zone_name');
                include 'tpls/admin/sectoredit.tpl.php';
            }

            if ($module === 'useredit' && $operation === 'edit') {
                $user = (int) fromGet('user', new \Exception('user'));

                $row = BNT\ShipFunc::shipById($user);
                include 'tpls/admin/useredit.tpl.php';
            }

            if ($module === 'planedit' && $operation === 'edit') {
                $planet = (int) fromGet('planet', new \Exception('planet'));
                $row = planetById($planet);
                $owners = db()->fetchAllKeyValue("SELECT ship_id,character_name FROM ships ORDER BY character_name");
                include 'tpls/admin/planetedit.tpl.php';
            }

            if ($module === 'zoneedit' && $operation === 'edit') {
                $zone = (int) fromGet('zone', new \Exception('zone'));
                $row = zoneById($zone);
                $zones = db()->fetchAllKeyValue('SELECT zone_id,zone_name FROM zones ORDER BY zone_name');
                include 'tpls/admin/zoneedit.tpl.php';
            }

            if (empty($module)) {
                $ships = db()->fetchAllKeyValue("SELECT ship_id,character_name FROM ships ORDER BY character_name");
                $sectors = db()->fetchAllKeyValue("SELECT sector_id, sector_id  AS value FROM universe ORDER BY sector_id");
                $planets = db()->fetchAllKeyValue("SELECT planet_id, CONCAT_WS(' in ', name, sector_id) FROM planets ORDER BY sector_id");
                $zones = db()->fetchAllKeyValue('SELECT zone_id,zone_name FROM zones ORDER BY zone_name');
                include 'tpls/admin/welcome.tpl.php';
            }

            break;
    }
} catch (Exception $ex) {
    switch (requestMethod()) {
        case 'GET':
            include 'tpls/admin/error.tpl.php';
            break;
        case 'POST':
            echo responseJsonByException($ex);
            break;
    }
}
