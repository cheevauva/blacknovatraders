<?php

use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\Link\DAO\LinkRetrieveManyByCriteriaDAO;
use BNT\Planet\DAO\PlanetRetrieveManyBySectorDAO;
use BNT\Traderoute\DAO\TraderouteRetrieveManyByShipDAO;
use BNT\SectorDefence\DAO\SectorDefenceRetrieveManyByCriteriaDAO;
use BNT\Zone\DAO\ZoneRetrieveByIdDAO;
use BNT\Ship\DAO\ShipRetrieveManyBySectorDAO;
use BNT\Planet\View\PlanetView;
use BNT\Ship\View\ShipView;
use BNT\Sector\View\SectorView;
use BNT\Traderoute\View\TraderouteView;
use BNT\SectorDefence\View\SectorDefenceView;

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

$retriveLinks = new LinkRetrieveManyByCriteriaDAO;
$retriveLinks->link_start = $playerinfo->sector;
$retriveLinks->serve();

$retrieveSectorDefences = new SectorDefenceRetrieveManyByCriteriaDAO;
$retrieveSectorDefences->sector_id = $playerinfo->sector;
$retrieveSectorDefences->serve();

echo twig()->render('main.twig', [
    'playerinfo' => new ShipView($playerinfo),
    'sectorinfo' => $sectorinfo ? new SectorView($sectorinfo) : null,
    'links' => $retriveLinks->links,
    'planetsInSector' => PlanetView::map(PlanetRetrieveManyBySectorDAO::call($playerinfo->sector)),
    'traderoutes' => TraderouteView::map(TraderouteRetrieveManyByShipDAO::call($playerinfo)),
    'defencesInSector' => SectorDefenceView::map($retrieveSectorDefences->defences),
    'zoneinfo' => $sectorinfo ? ZoneRetrieveByIdDAO::call($sectorinfo->zone_id) : null,
    'shipsInSector' => ShipView::map(ShipRetrieveManyBySectorDAO::call($playerinfo->sector)),
]);
