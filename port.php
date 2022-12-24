<?php

use BNT\Sector\SectorPortTypeEnum;
use BNT\Bounty\DAO\BountySumByShipDAO;
use BNT\Sector\Servant\SectorPortTradeWithShipServant;
use BNT\Zone\Servant\ZonePortTradeServant;
use BNT\Zone\Exception\ZoneException;
use BNT\Sector\Servant\SectorPortSpecialServant;

include("config.php");

loadlanguage($lang);

connectdb();

if (isNotAuthorized()) {
    die();
}

$playerinfo = ship();
$sectorinfo = \BNT\Sector\DAO\SectorRetrieveByIdDAO::call($playerinfo->sector);
$zoneinfo = \BNT\Zone\DAO\ZoneRetrieveByIdDAO::call($sectorinfo->zone_id);

function dropdown($element_name, $current_value): string
{
    $i = $current_value;
    $dropdownvar = "<select size='1' name='$element_name'";
    $dropdownvar = "$dropdownvar>\n";
    while ($i < 60) {
        if ($current_value == $i) {
            $dropdownvar = "$dropdownvar        <option value='$i' selected>$i</option>\n";
        } else {
            $dropdownvar = "$dropdownvar        <option value='$i'>$i</option>\n";
        }
        $i++;
    }
    $dropdownvar = "$dropdownvar       </select>\n";
    return $dropdownvar;
}

$title = $l_title_port;

include 'header.php';

try {
    ZonePortTradeServant::call($zoneinfo, $playerinfo);

    switch ($sectorinfo->port_type) {
        case SectorPortTypeEnum::Energy:
        case SectorPortTypeEnum::Goods:
        case SectorPortTypeEnum::Ore:
        case SectorPortTypeEnum::Organics:
            $calculator = SectorPortTradeWithShipServant::call($sectorinfo, $playerinfo);
            include './port_resource.php';
            break;
        case SectorPortTypeEnum::Special:
            $totalBounty = BountySumByShipDAO::call($playerinfo)->total;
            $portSpecial = SectorPortSpecialServant::call($playerinfo);
            include './port_special.php';
            break;
        case SectorPortTypeEnum::None:
        default:
            echo $l_noport;
            break;
    }
} catch (ZoneException $ex) {
    $title = $l_no_trade;
    bigtitle();
    echo $ex->getMessage() . '<br>';
    TEXT_GOTOMAIN();
}

include './footer.php';

