<?php

use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\Link\DAO\LinkRetrieveManyBySectorDAO;
use BNT\Planet\DAO\PlanetRetrieveManyBySectorDAO;
use BNT\Traderoute\DAO\TraderouteRetrieveManyByShipDAO;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyBySectorDAO;
use BNT\Zone\DAO\ZoneRetrieveByIdDAO;
use BNT\Ship\DAO\ShipRetrieveManyBySectorDAO;
use BNT\Planet\View\PlanetView;
use BNT\Ship\View\ShipView;
use BNT\Sector\View\SectorView;
use BNT\Traderoute\View\TraderouteView;
use BNT\Sector\Sector;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$playerinfo = ship();

if (!empty($playerinfo->cleared_defences)) {
    header('Location: ' . $playerinfo->cleared_defences);
    die;
}

if ($playerinfo->on_planet) {
    $currentPlanet = BNT\Planet\DAO\PlanetRetrieveByIdDAO::call($playerinfo->planet_id);

    if (!$currentPlanet) {
        $playerinfo->on_planet = false;
        shipSave($playerinfo);
    } else {
        header('Location: planet.php?planet_id=' . $currentPlanet->planet_id . '&ship_id=' . $playerinfo->ship_id);
        die;
    }
}

$sectorinfo = SectorRetrieveByIdDAO::call($playerinfo->sector);

echo twig()->render('main.twig', [
    'playerinfo' => new ShipView($playerinfo),
    'sectorinfo' => new SectorView($sectorinfo),
    'links' => LinkRetrieveManyBySectorDAO::call($playerinfo->sector),
    'planetsInSector' => PlanetView::map(PlanetRetrieveManyBySectorDAO::call($playerinfo->sector)),
    'traderoutes' => TraderouteView::map(TraderouteRetrieveManyByShipDAO::call($playerinfo)),
    'defencesInSector' => SectorDefenceRetrieveManyBySectorDAO::call($playerinfo->sector),
    'zoneinfo' => ZoneRetrieveByIdDAO::call($sectorinfo->zone_id),
    'shipsInSector' => ShipView::map(ShipRetrieveManyBySectorDAO::call($playerinfo->sector)),
]);
