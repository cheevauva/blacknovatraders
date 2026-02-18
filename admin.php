<?php

use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Config\DAO\ConfigUpdateDAO;
use BNT\Planet\DAO\PlanetByIdDAO;
use BNT\Planet\DAO\PlanetUpdateDAO;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Sector\DAO\SectorUpdateDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Zone\DAO\ZoneUpdateDAO;

$disableRegisterGlobalFix = true;

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

    if ($userinfo['role'] !== 'admin') {
        throw new \Exception('You not admin');
    }

    switch (requestMethod()) {
        case 'POST':
            if ($module === 'sectoredit' && $operation === 'save') {
                $sector = (int) fromGET('sector', new \Exception('sector'));

                SectorUpdateDAO::call($container, [
                    'sector_name' => (string) fromPOST('sector_name'),
                    'zone_id' => (int) fromPOST('zone_id'),
                    'beacon' => (string) fromPOST('beacon'),
                    'port_type' => (string) fromPOST('port_type'),
                    'port_organics' => (int) fromPOST('port_organics'),
                    'port_ore' => (int) fromPOST('port_ore'),
                    'port_goods' => (int) fromPOST('port_goods'),
                    'port_energy' => (int) fromPOST('port_energy'),
                    'distance' => (int) fromPOST('distance'),
                    'angle1' => (float) fromPOST('angle1'),
                    'angle2' => (float) fromPOST('angle2'),
                ], $sector);
            }

            if ($module === 'univedit' && $operation === 'doexpand') {
                $radius = (int) fromPOST('radius', new \Exception('radius'));
                $configUpdate = ConfigUpdateDAO::call($container, [
                    'universe_size' => $universe_size,
                ]);

                db()->q('UPDATE universe SET distance = FLOOR(RAND() * :radius) WHERE 1 = 1', [
                    'radius' => $radius + 1,
                ]);
            }

            if ($module === 'useredit' && $operation === 'save') {
                $user = (int) fromGet('user', new \Exception('user'));
                $row = ShipByIdDAO::call($container, $user)->ship;

                ShipUpdateDAO::call($container, [
                    'character_name' => (string) fromPOST('character_name', new \Exception('character_name')),
                    'password' => fromPOST('password2') ? md5((string) fromPOST('password2')) : $row['password'],
                    'email' => (string) fromPOST('email', new \Exception('email')),
                    'ship_name' => (string) fromPOST('ship_name', new \Exception('ship_name')),
                    'ship_destroyed' => !fromPOST('ship_destroyed') ? 'N' : 'Y',
                    'hull' => (int) fromPOST('hull', 0),
                    'engines' => (int) fromPOST('engines', 0),
                    'power' => (int) fromPOST('power', 0),
                    'computer' => (int) fromPOST('computer', 0),
                    'sensors' => (int) fromPOST('sensors', 0),
                    'armor' => (int) fromPOST('armor', 0),
                    'shields' => (int) fromPOST('shields', 0),
                    'beams' => (int) fromPOST('beams', 0),
                    'torp_launchers' => (int) fromPOST('torp_launchers', 0),
                    'cloak' => (int) fromPOST('cloak', 0),
                    'credits' => (int) fromPOST('credits', 0),
                    'turns' => (int) fromPOST('turns', 0),
                    'dev_warpedit' => (int) fromPOST('dev_warpedit'),
                    'dev_genesis' => (int) fromPOST('dev_genesis'),
                    'dev_beacon' => (int) fromPOST('dev_beacon'),
                    'dev_emerwarp' => (int) fromPOST('dev_emerwarp'),
                    'dev_escapepod' => !fromPOST('dev_escapepod') ? 'N' : 'Y',
                    'dev_fuelscoop' => !fromPOST('dev_fuelscoop') ? 'N' : 'Y',
                    'dev_minedeflector' => (int) fromPOST('dev_minedeflector'),
                    'sector' => (int) fromPOST('sector'),
                    'ship_ore' => (int) fromPOST('ship_ore'),
                    'ship_organics' => (int) fromPOST('ship_organics'),
                    'ship_goods' => (int) fromPOST('ship_goods'),
                    'ship_energy' => (int) fromPOST('ship_energy'),
                    'ship_colonists' => (int) fromPOST('ship_colonists'),
                    'ship_fighters' => (int) fromPOST('ship_fighters'),
                    'torps' => (int) fromPOST('torps'),
                    'armor_pts' => (int) fromPOST('armor_pts'),
                ], $user);
            }

            if ($module === 'planedit' && $operation === 'save') {
                $planet = (int) fromGet('planet', new \Exception('planet'));

                PlanetUpdateDAO::call($container, [
                    'sector_id' => (int) fromPOST('sector_id'),
                    'defeated' => !fromPOST('defeated') ? 'N' : 'Y',
                    'name' => (string) fromPOST('name'),
                    'base' => !fromPOST('base') ? 'N' : 'Y',
                    'sells' => !fromPOST('sells') ? 'N' : 'Y',
                    'owner' => (string) fromPOST('owner'),
                    'organics' => (int) fromPOST('organics', 0),
                    'ore' => (int) fromPOST('ore', 0),
                    'goods' => (int) fromPOST('goods', 0),
                    'energy' => (int) fromPOST('energy', 0),
                    'corp' => (string) fromPOST('corp'),
                    'colonists' => (int) fromPOST('colonists', 0),
                    'credits' => (int) fromPOST('credits', 0),
                    'fighters' => (int) fromPOST('fighters', 0),
                    'torps' => (int) fromPOST('torps', 0),
                    'prod_organics' => (int) fromPOST('prod_organics', 0),
                    'prod_ore' => (int) fromPOST('prod_ore', 0),
                    'prod_goods' => (int) fromPOST('prod_goods', 0),
                    'prod_energy' => (int) fromPOST('prod_energy', 0),
                    'prod_fighters' => (int) fromPOST('prod_fighters', 0),
                    'prod_torp' => (int) fromPOST('prod_torp', 0)
                ], $planet);
            }

            if ($module === 'zoneedit' && $operation === 'save') {
                $zone = (int) fromGet('zone', new \Exception('zone'));

                ZoneUpdateDAO::call($container, [
                    'zone_name' => (string) fromPOST('zone_name'),
                    'allow_beacon' => !fromPOST('zone_beacon') ? 'N' : 'Y',
                    'allow_attack' => !fromPOST('zone_attack') ? 'N' : 'Y',
                    'allow_warpedit' => !fromPOST('zone_warpedit') ? 'N' : 'Y',
                    'allow_planet' => !fromPOST('zone_planet') ? 'N' : 'Y',
                    'max_hull' => (int) fromPOST('zone_hull', 0)
                ], $zone);
            }

            redirectTo('admin.php');
            break;
        case 'GET':
            if ($module === 'sectoredit' && $operation === 'edit') {
                $sector = (int) fromGet('sector', new \Exception('sector'));

                $row = SectorByIdDAO::call($container, $sector)->sector;
                $zones = db()->fetchAllKeyValue('SELECT zone_id, zone_name FROM zones ORDER BY zone_name');
                include 'tpls/admin/sectoredit.tpl.php';
            }

            if ($module === 'useredit' && $operation === 'edit') {
                $user = (int) fromGet('user', new \Exception('user'));

                $row = ShipByIdDAO::call($container, $user)->ship;
                include 'tpls/admin/useredit.tpl.php';
            }

            if ($module === 'planedit' && $operation === 'edit') {
                $planet = (int) fromGet('planet', new \Exception('planet'));
                $row = PlanetByIdDAO::call($container, $planet)->planet;
                $owners = db()->fetchAllKeyValue("SELECT ship_id,character_name FROM ships ORDER BY character_name");
                include 'tpls/admin/planetedit.tpl.php';
            }

            if ($module === 'zoneedit' && $operation === 'edit') {
                $zone = (int) fromGet('zone', new \Exception('zone'));
                $row = ZoneByIdDAO::call($container, $zone)->zone;
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
