<?php

use BNT\Sector\Servant\SectorPortSpecialOfferServant;
use BNT\Sector\Servant\SectorPortSpecialPurchaseServant;
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
    $color_red = "red";
    $color_green = "#00FF00"; //light green
    $trade_deficit = "$l_cost : ";
    $trade_benefit = "$l_profit : ";
    /*
      Here is the TRADE fonction to strip out some "spaghetti code".
      The function saves about 60 lines of code, I hope it will be
      easier to modify/add something in this part.
      --Fant0m
     */
    $price_array = array();

    function TRADE($price, $delta, $max, $limit, $factor, $port_type, $origin)
    {
        global $trade_color, $trade_deficit, $trade_result, $trade_benefit, $sectorinfo, $color_green, $color_red, $price_array;

        if ($sectorinfo[port_type] == $port_type) {
            $price_array[$port_type] = $price - $delta * $max / $limit * $factor;
        } else {
            $price_array[$port_type] = $price + $delta * $max / $limit * $factor;
            $origin = -$origin;
        }
        /* debug info
          print "$origin*$price_array[$port_type]=";
          print $origin*$price_array[$port_type]."<br>";
         */
        return $origin;
    }

    $trade_ore = round(abs($trade_ore));
    $trade_organics = round(abs($trade_organics));
    $trade_goods = round(abs($trade_goods));
    $trade_energy = round(abs($trade_energy));

    $trade_ore = TRADE($ore_price, $ore_delta, $sectorinfo[port_ore], $ore_limit, $inventory_factor, "ore", $trade_ore);
    $trade_organics = TRADE($organics_price, $organics_delta, $sectorinfo[port_organics], $organics_limit, $inventory_factor, "organics", $trade_organics);
    $trade_goods = TRADE($goods_price, $goods_delta, $sectorinfo[port_goods], $goods_limit, $inventory_factor, "goods", $trade_goods);
    $trade_energy = TRADE($energy_price, $energy_delta, $sectorinfo[port_energy], $energy_limit, $inventory_factor, "energy", $trade_energy);

    $ore_price = $price_array['ore'];
    $organics_price = $price_array['organics'];
    $goods_price = $price_array['goods'];
    $energy_price = $price_array['energy'];

    $cargo_exchanged = $trade_ore + $trade_organics + $trade_goods;

    $free_holds = NUM_HOLDS($playerinfo->hull) - $playerinfo[ship_ore] - $playerinfo[ship_organics] -
    $playerinfo[ship_goods] - $playerinfo[ship_colonists];
    $free_power = NUM_ENERGY($playerinfo->power) - $playerinfo[ship_energy];
    $total_cost = $trade_ore * $ore_price + $trade_organics * $organics_price + $trade_goods * $goods_price +
    $trade_energy * $energy_price;

    /* debug info
      echo "$trade_ore * $ore_price + $trade_organics * $organics_price + $trade_goods * $goods_price + $trade_energy * $energy_price = $total_cost";
     */

    if ($free_holds < $cargo_exchanged) {
        echo "$l_notenough_cargo  $l_returnto_port<BR><BR>";
    } elseif ($trade_energy > $free_power) {
        echo "$l_notenough_power  $l_returnto_port<BR><BR>";
    } elseif ($playerinfo[turns] < 1) {
        echo "$l_notenough_turns.<BR><BR>";
    } elseif ($playerinfo[credits] < $total_cost) {
        echo "$l_notenough_credits <BR><BR>";
    } elseif ($trade_ore < 0 && abs($playerinfo[ship_ore]) < abs($trade_ore)) {
        echo "$l_notenough_ore ";
    } elseif ($trade_organics < 0 && abs($playerinfo[ship_organics]) < abs($trade_organics)) {
        echo "$l_notenough_organics ";
    } elseif ($trade_goods < 0 && abs($playerinfo[ship_goods]) < abs($trade_goods)) {
        echo "$l_notenough_goods ";
    } elseif ($trade_energy < 0 && abs($playerinfo[ship_energy]) < abs($trade_energy)) {
        echo "$l_notenough_energy ";
    } elseif (abs($trade_organics) > $sectorinfo[port_organics]) {
        echo $l_exceed_organics;
    } elseif (abs($trade_ore) > $sectorinfo[port_ore]) {
        echo $l_exceed_ore;
    } elseif (abs($trade_goods) > $sectorinfo[port_goods]) {
        echo $l_exceed_goods;
    } elseif (abs($trade_energy) > $sectorinfo[port_energy]) {
        echo $l_exceed_energy;
    } else {

        if ($total_cost == 0) {
            $trade_color = "white";
            $trade_result = "$l_cost : ";
        } elseif ($total_cost < 0) {
            $trade_color = $color_green;
            $trade_result = $trade_benefit;
        } else {
            $trade_color = $color_red;
            $trade_result = $trade_deficit;
        }

        echo "
      <TABLE BORDER=2 CELLSPACING=2 CELLPADDING=2 BGCOLOR=#400040 WIDTH=600 ALIGN=CENTER>
         <TR>
            <TD colspan=99 align=center><font size=3 color=white><b>$l_trade_result</b></font></TD>
         </TR>
         <TR>
            <TD colspan=99 align=center><b><font color=\"" . $trade_color . "\">" . $trade_result . " " . NUMBER(abs($total_cost)) . " $l_credits</font></b></TD>
         </TR>
         <TR bgcolor=$color_line1>
            <TD><b><font size=2 color=white>$l_traded_ore: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_ore) . "</font></b></TD>
         </TR>
         <TR bgcolor=$color_line2>
            <TD><b><font size=2 color=white>$l_traded_organics: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_organics) . "</font></b></TD>
         </TR>
         <TR bgcolor=$color_line1>
            <TD><b><font size=2 color=white>$l_traded_goods: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_goods) . "</font></b></TD>
         </TR>
         <TR bgcolor=$color_line2>
            <TD><b><font size=2 color=white>$l_traded_energy: </font><b></TD><TD align=right><b><font size=2 color=white>" . NUMBER($trade_energy) . "</font></b></TD>
         </TR>
      </TABLE>
      ";

        /* Update ship cargo, credits and turns */
        $trade_result = $db->Execute("UPDATE $dbtables[ships] SET turns=turns-1, turns_used=turns_used+1, rating=rating+1, credits=credits-$total_cost, ship_ore=ship_ore+$trade_ore, ship_organics=ship_organics+$trade_organics, ship_goods=ship_goods+$trade_goods, ship_energy=ship_energy+$trade_energy where ship_id=$playerinfo[ship_id]");

        /* Make all trades positive to change port values */
        $trade_ore = round(abs($trade_ore));
        $trade_organics = round(abs($trade_organics));
        $trade_goods = round(abs($trade_goods));
        $trade_energy = round(abs($trade_energy));

        /* Decrease supply and demand on port */
        $trade_result2 = $db->Execute("UPDATE $dbtables[universe] SET port_ore=port_ore-$trade_ore, port_organics=port_organics-$trade_organics, port_goods=port_goods-$trade_goods, port_energy=port_energy-$trade_energy where sector_id=$sectorinfo[sector_id]");

        echo "$l_trade_complete.<BR><BR>";
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
