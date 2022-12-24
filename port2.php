<?php

use BNT\Sector\Servant\SectorPortSpecialOfferServant;
use BNT\Sector\Servant\SectorPortSpecialPurchaseServant;
use BNT\Sector\Servant\SectorPortResourceOfferServant;
use BNT\Sector\Servant\SectorPortResourcePurchaseServant;
use BNT\Zone\Servant\ZonePortTradeServant;
use BNT\Sector\SectorPortTypeEnum;

include("config.php");
updatecookie();

loadlanguage($lang);

$title = $l_title_port;

connectdb();

if (isNotAuthorized()) {
    die();
}


$playerinfo = ship();
$sectorinfo = \BNT\Sector\DAO\SectorRetrieveByIdDAO::call($playerinfo->sector);
$zoneinfo = \BNT\Zone\DAO\ZoneRetrieveByIdDAO::call($sectorinfo->zone_id);

ZonePortTradeServant::call($zoneinfo, $playerinfo);

include 'header.php';

bigtitle();

if ($sectorinfo->port_type === SectorPortTypeEnum::Special) {
    if (isLoanPending($playerinfo->ship_id)) {
        echo "$l_port_loannotrade<p>";
        echo "<A HREF=igb.php>$l_igb_term</a><p>";
        goto footer;
    }


    try {
        $offer = new SectorPortSpecialOfferServant;
        foreach (get_object_vars($offer) as $prop => $value) {
            if (isset($_POST[$prop])) {
                $offer->{$prop} = intval($_POST[$prop]);
            }
        }
        $offer->ship = $playerinfo;
        $offer->serve();

        $purchase = SectorPortSpecialPurchaseServant::call($offer);
        $purchase->serve();
        $trade_credits = NUMBER(abs($offer->total_cost));
        require_once './port2_special.php';
    } catch (\Exception $ex) {
        echo $ex->getMessage();
    }
}

if ($sectorinfo->port_type !== SectorPortTypeEnum::Special && $sectorinfo->port_type !== SectorPortTypeEnum::None) {
    try {
        print_r($_POST);
        $offerResource = SectorPortResourceOfferServant::call($sectorinfo);
        $offerResource->trade_energy = intval($_POST['trade_energy'] ?? 0);
        $offerResource->trade_goods = intval($_POST['trade_goods'] ?? 0);
        $offerResource->trade_ore = intval($_POST['trade_ore'] ?? 0);
        $offerResource->trade_organics = intval($_POST['trade_organics'] ?? 0);
        $offerResource->serve();

        SectorPortResourcePurchaseServant::call($offerResource, $playerinfo);
        require_once './port2_resource.php';
    } catch (\Exception $ex) {
        echo $ex->getMessage();
    }
}
footer:

echo "<BR><BR>";
TEXT_GOTOMAIN();

if ($sectorinfo->port_type === SectorPortTypeEnum::Special) {
    echo "<BR><BR>Click <A HREF=port.php>here</A> to return to the supply depot.";
}

include 'footer.php';
?>
