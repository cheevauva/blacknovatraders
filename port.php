<?php

use BNT\Sector\Enum\SectorPortTypeEnum;
use BNT\Bounty\DAO\BountySumByShipDAO;
use BNT\Sector\Servant\SectorPortResourcePreOfferServant;
use BNT\Sector\Servant\SectorPortSpecialServant;
use BNT\Zone\Servant\ZonePortTradeServant;
use BNT\Zone\DAO\ZoneRetrieveByIdDAO;
use BNT\Sector\DAO\SectorRetrieveByIdDAO;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$playerinfo = ship();
$sectorinfo = SectorRetrieveByIdDAO::call($container, $playerinfo->sector);
$zoneinfo = ZoneRetrieveByIdDAO::call($container, $sectorinfo->zone_id);

ZonePortTradeServant::call($container, $zoneinfo, $playerinfo);

switch ($sectorinfo->port_type) {
    case SectorPortTypeEnum::Energy:
    case SectorPortTypeEnum::Goods:
    case SectorPortTypeEnum::Ore:
    case SectorPortTypeEnum::Organics:
        echo twig()->render('port/port_resource.twig', [
            'sector' => $sectorinfo,
            'calculator' => SectorPortResourcePreOfferServant::call($container, $sectorinfo, $playerinfo),
            'freeHolds' => $playerinfo->getFreeHolds(),
            'freePower' => $playerinfo->getFreePower(),
            'credits' => $playerinfo->credits,
        ]);
        break;
    case SectorPortTypeEnum::Special:
        $totalBounty = BountySumByShipDAO::call($container, $playerinfo)->total;
        
        if ($totalBounty > 0) {
            echo twig()->render('port/port_special_unpaid_bounty.twig', [
                'totalBounty' => BountySumByShipDAO::call($container, $playerinfo)->total,
            ]);
            return;
        }
        
        echo twig()->render('port/port_special.twig', [
            'ship' => $playerinfo,
            'sector' => $sectorinfo,
            'calculator' => SectorPortSpecialServant::call($container, $playerinfo),
        ]);
        break;
    case SectorPortTypeEnum::None:
    default:
        echo twig()->render('port/port_none.twig');
        break;
}