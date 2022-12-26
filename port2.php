<?php

use BNT\Sector\Servant\SectorPortSpecialOfferServant;
use BNT\Sector\Servant\SectorPortSpecialPurchaseServant;
use BNT\Sector\Servant\SectorPortResourceOfferServant;
use BNT\Sector\Servant\SectorPortResourcePurchaseServant;
use BNT\Zone\Servant\ZonePortTradeServant;
use BNT\Sector\SectorPortTypeEnum;
use BNT\Sector\DAO\SectorRetrieveByIdDAO;
use BNT\Zone\DAO\ZoneRetrieveByIdDAO;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$playerinfo = ship();
$sectorinfo = SectorRetrieveByIdDAO::call($playerinfo->sector);
$zoneinfo = ZoneRetrieveByIdDAO::call($sectorinfo->zone_id);

ZonePortTradeServant::call($zoneinfo, $playerinfo);

switch ($sectorinfo->port_type) {
    case SectorPortTypeEnum::Special:
        if (isLoanPending($playerinfo->ship_id)) {
            echo twig()->render('port/port2_special_loannotrade.twig', [
                'offer' => $offer,
                'trade_credits' => abs($offer->total_cost),
            ]);
            return;
        }

        $offer = new SectorPortSpecialOfferServant;
        $offer->dev_genesis_number = abs(intval($_POST['dev_genesis_number']));
        $offer->dev_beacon_number = abs(intval($_POST['dev_beacon_number']));
        $offer->dev_emerwarp_number = abs(intval($_POST['dev_emerwarp_number']));
        $offer->dev_warpedit_number = abs(intval($_POST['dev_warpedit_number']));
        $offer->dev_minedeflector_number = abs(intval($_POST['dev_minedeflector_number']));
        $offer->escapepod_purchase = abs(intval($_POST['escapepod_purchase']));
        $offer->fuelscoop_purchase = abs(intval($_POST['fuelscoop_purchase']));
        $offer->lssd_purchase = abs(intval($_POST['lssd_purchase']));
        $offer->computer_upgrade = abs(intval($_POST['computer_upgrade']));
        $offer->hull_upgrade = abs(intval($_POST['hull_upgrade']));
        $offer->engine_upgrade = abs(intval($_POST['engine_upgrade']));
        $offer->power_upgrade = abs(intval($_POST['power_upgrade']));
        $offer->sensors_upgrade = abs(intval($_POST['']));
        $offer->beams_upgrade = abs(intval($_POST['sensors_upgrade']));
        $offer->armor_upgrade = abs(intval($_POST['armor_upgrade']));
        $offer->cloak_upgrade = abs(intval($_POST['cloak_upgrade']));
        $offer->torp_launchers_upgrade = abs(intval($_POST['torp_launchers_upgrade']));
        $offer->shields_upgrade = abs(intval($_POST['shields_upgrade']));
        $offer->fighter_number = abs(intval($_POST['fighter_number']));
        $offer->torpedo_number = abs(intval($_POST['torpedo_number']));
        $offer->armor_number = abs(intval($_POST['armor_number']));
        $offer->colonist_number = abs(intval($_POST['colonist_number']));
        $offer->ship = $playerinfo;
        $offer->serve();

        $purchase = SectorPortSpecialPurchaseServant::call($offer);
        $purchase->serve();

        echo twig()->render('port/port2_special.twig', [
            'offer' => $offer,
            'trade_credits' => abs($offer->total_cost),
        ]);
        break;
    case SectorPortTypeEnum::Ore:
    case SectorPortTypeEnum::Organics:
    case SectorPortTypeEnum::Goods:
    case SectorPortTypeEnum::Energy:
        $offerResource = new SectorPortResourceOfferServant;
        $offerResource->sector = $sectorinfo;
        $offerResource->needle_trade_energy = intval(abs($_POST['trade_energy'] ?? 0));
        $offerResource->needle_trade_goods = intval(abs($_POST['trade_goods'] ?? 0));
        $offerResource->needle_trade_ore = intval(abs($_POST['trade_ore'] ?? 0));
        $offerResource->needle_trade_organics = intval(abs($_POST['trade_organics'] ?? 0));
        $offerResource->serve();

        SectorPortResourcePurchaseServant::call($offerResource, $playerinfo);

        echo twig()->render('port/port2_resource.twig', [
            'offerResource' => $offerResource,
        ]);
        break;
}
