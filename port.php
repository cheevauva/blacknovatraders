<?
include("config.php");

loadlanguage($lang);
$title = $l_title_port;
include("header.php");

connectdb();

if (isNotAuthorized()) {
    die();
}

$playerinfo = ship();
$sectorinfo = \BNT\Sector\DAO\SectorRetrieveByIdDAO::call($playerinfo->sector);
$zoneinfo = \BNT\Zone\DAO\ZoneRetrieveByIdDAO::call($sectorinfo->zone_id);

if ($zoneinfo->zone_id == 4) {
    $title = $l_sector_war;
    bigtitle();
    echo "$l_war_info <p>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
} elseif (is_bool($zoneinfo->allow_trade) && !$zoneinfo->allow_trade) {
    $title = "Trade forbidden";
    bigtitle();
    echo "$l_no_trade_info<p>";
    TEXT_GOTOMAIN();
    include("footer.php");
    die();
} elseif ($zoneinfo->allow_trade == 'L') {
    if ($zoneinfo->corp_zone == 'N') {
        $res = $db->Execute("SELECT team FROM $dbtables[ships] WHERE ship_id=$zoneinfo->owner]");
        $ownerinfo = $res->fields;

        if ($playerinfo->ship_id != $zoneinfo->owner && $playerinfo->team == 0 || $playerinfo->team != $ownerinfo->team) {
            $title = "Trade forbidden";
            bigtitle();
            echo "Trading at this port is not allowed for outsiders<p>";
            TEXT_GOTOMAIN();
            include("footer.php");
            die();
        }
    } else {
        if ($playerinfo->team != $zoneinfo->owner) {
            $title = $l_no_trade;
            bigtitle();
            echo "$l_no_trade_out<p>";
            TEXT_GOTOMAIN();
            include("footer.php");
            die();
        }
    }
}

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

?>
<?php switch ($sectorinfo->port_type): ?>
<?php case BNT\Sector\SectorPortTypeEnum::Energy: ?>
<?php case BNT\Sector\SectorPortTypeEnum::Goods: ?>
<?php case BNT\Sector\SectorPortTypeEnum::Ore: ?>
<?php case BNT\Sector\SectorPortTypeEnum::Organics: ?>
    <?php $calculator = \BNT\Sector\Servant\SectorPortTradeWithShipServant::call($sectorinfo, $playerinfo); ?>
    <?php bigtitle($l_title_trade); ?>
    <FORM ACTION=port2.php METHOD=POST>
        <table>
            <tr>
                <td><?php echo $l_commodity; ?></td>
                <td><?php echo $l_buying; ?>/<?php echo $l_selling; ?></td>
                <td><?php echo $l_amount; ?></td>
                <td><?php echo $l_price; ?></td>
                <td><?php echo $l_buy; ?>/<?php echo $l_sell; ?></td>
                <td><?php echo $l_cargo; ?></td>
            </tr>
            <tr>
                <td><?php echo $l_ore; ?></td>
                <td><?php echo $sectorinfo->port_type == BNT\Sector\SectorPortTypeEnum::Ore ? $l_selling : $l_buying; ?></td>
                <td><?php echo NUMBER($sectorinfo->port_ore); ?></td>
                <td><?php echo $calculator->ore_price; ?></td>
                <td>
                    <INPUT TYPE=TEXT NAME=trade_ore SIZE=10 MAXLENGTH=20 VALUE="<?php echo $calculator->ore_amount; ?>">
                </td>
                <td><?php echo NUMBER($playerinfo->ship_ore); ?></td>
            </tr>
            <tr>
                <td><?php echo $l_organics; ?></td>
                <td><?php echo $sectorinfo->port_type == BNT\Sector\SectorPortTypeEnum::Organics ? $l_selling : $l_buying; ?></td>
                <td><?php echo NUMBER($sectorinfo->port_organics); ?></td>
                <td><?php echo $calculator->organics_price; ?></td>
                <td><INPUT TYPE=TEXT NAME=trade_organics SIZE=10 MAXLENGTH=20 VALUE="<?php echo $calculator->organics_amount; ?>"></td>
                <td><?php echo NUMBER($playerinfo->ship_organics); ?></td>
            </tr>
            <tr>
                <td><?php echo $l_goods; ?></td>
                <td><?php echo $sectorinfo->port_type == BNT\Sector\SectorPortTypeEnum::Goods ? $l_selling : $l_buying; ?></td>
                <td><?php echo NUMBER($sectorinfo->port_goods); ?></td>
                <td><?php echo $calculator->goods_price; ?></td>
                <td><INPUT TYPE=TEXT NAME=trade_goods SIZE=10 MAXLENGTH=20 VALUE="<?php echo $calculator->goods_amount; ?>"></td>
                <td><?php echo NUMBER($playerinfo->ship_goods); ?></td>
            </tr>
            <tr>
                <td><?php echo $l_energy; ?></td>
                <td><?php echo $sectorinfo->port_type == BNT\Sector\SectorPortTypeEnum::Energy ? $l_selling : $l_buying; ?></td>
                <td><?php echo NUMBER($sectorinfo->port_energy); ?></td>
                <td><?php echo $calculator->energy_price; ?></td>
                <td><INPUT TYPE=TEXT NAME=trade_energy SIZE=10 MAXLENGTH=20 VALUE="<?php echo $calculator->energy_amount; ?>"></td>
                <td><?php echo NUMBER($playerinfo->ship_energy); ?></td>
            </tr>
        </table>
        <INPUT TYPE=SUBMIT VALUE="<?php echo $l_trade; ?>">
    </FORM>
    <?php echo strtr($l_trade_st_info, [
        '[free_holds]' => NUMBER($playerinfo->getFreeHolds()),
        '[free_power]' => NUMBER($playerinfo->getFreePower()),
        '[credits]' => NUMBER($playerinfo->credits),
    ]);
    ?>
    <?php break; ?>
<?php case BNT\Sector\SectorPortTypeEnum::Special: ?>
<A HREF=\"bounty.php\">$l_by_placebounty</A><BR>
<FORM ACTION=port2.php METHOD=POST>
    <TABLE>
        <TR>
            <td><B><?php echo $l_device; ?></B></td>
            <td><B><?php echo $l_cost; ?></B></td>
            <td><B><?php echo $l_current; ?></B></td>
            <td><B><?php echo $l_max; ?></B></td>
            <td><B><?php echo $l_qty; ?></B></td>
            <td><B><?php echo $l_ship_levels; ?></B></td>
            <td><B><?php echo $l_cost; ?></B></td>
            <td><B><?php echo $l_current; ?></B></td>
            <td><B><?php echo $l_upgrade; ?></B></td>
        </TR>
        <TR>
            <td>$l_genesis</td>
            <td><?php echo NUMBER($dev_genesis_price); ?></td>
            <td><?php echo NUMBER($playerinfo->dev_genesis); ?></td>
            <td><?php echo $l_unlimited;?></td>
            <td><INPUT TYPE=TEXT NAME=dev_genesis_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur></td>
            <td>$l_hull</td>
            <td><input type=text readonly class='portcosts1' name=hull_costper VALUE='0' tabindex='-1' $onblur></td>
            <td><?php echo NUMBER($playerinfo->hull); ?></td>
            <td>
                <?php echo dropdown("hull_upgrade", $playerinfo->hull); ?>
            </td>
        </TR>
        <TR>
            <td>$l_beacons</td>
            <td><?php echo NUMBER($dev_beacon_price); ?></td>
            <td><?php echo NUMBER($playerinfo->dev_beacon); ?></td>
            <td><?php echo $l_unlimited;?></td>
            <td><INPUT TYPE=TEXT NAME=dev_beacon_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur></td>
            <td>$l_engines</td>
            <td><input type=text readonly class='portcosts2' size=10 name=engine_costper VALUE='0' tabindex='-1' $onblur></td>
            <td><?php echo NUMBER($playerinfo->engines); ?></td>
            <td>
                <?php echo dropdown("engine_upgrade", $playerinfo->engines); ?>
            </td>
        </TR>
        <TR >
            <td>$l_ewd</td>
            <td><?php echo NUMBER($dev_emerwarp_price); ?></td>
            <td><?php echo NUMBER($playerinfo->dev_emerwarp); ?></td>

            <?php if ($playerinfo->dev_emerwarp != $max_emerwarp) : ?>
                <td>
                    <a href='#'><?php echo NUMBER($emerwarp_free); ?></a>
                </td>
                <td><INPUT TYPE=TEXT NAME=dev_emerwarp_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur>
                <?php else : ?>
                <td>0</td>
                <td><input type=text readonly class='portcosts1' NAME=dev_emerwarp_number MAXLENGTH=10 value="<?php echo $l_full;?>" $onblur tabindex='-1'>
                <?php endif; ?>
            </td>
            <td>$l_power</td>
            <td><input type=text readonly class='portcosts1' name=power_costper VALUE='0' tabindex='-1' $onblur></td>
            <td><?php echo NUMBER($playerinfo->power); ?></td>
            <td>
                <?php echo dropdown("power_upgrade", $playerinfo->power);?>
            </td>
        </TR>
        <TR>
            <td>$l_warpedit</td>
            <td><?php echo NUMBER($dev_warpedit_price); ?></td>
            <td><?php echo NUMBER($playerinfo->dev_warpedit); ?></td><td><?php echo $l_unlimited;?></td><td><INPUT TYPE=TEXT NAME=dev_warpedit_number SIZE=4 MAXLENGTH=4 VALUE=0 $onblur></td>
            <td>$l_computer</td>
            <td><input type=text readonly class='portcosts2' name=computer_costper VALUE='0' tabindex='-1' $onblur></td>
            <td><?php echo NUMBER($playerinfo->computer); ?></td>
            <td>
                <?php echo dropdown("computer_upgrade", $playerinfo->computer);?>
            </td>
        </TR>
        <TR>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>$l_sensors</td>
            <td><input type=text readonly class='portcosts1' name=sensors_costper VALUE='0' tabindex='-1' $onblur></td>
            <td><?php echo NUMBER($playerinfo->sensors); ?></td>
            <td>
                <?php echo dropdown("sensors_upgrade", $playerinfo->sensors);?>
            </td>
        </TR>
        <TR >
            <td>$l_deflect</td>
            <td><?php echo NUMBER($dev_minedeflector_price); ?></td>
            <td><?php echo NUMBER($playerinfo->dev_minedeflector); ?></td>
            <td><?php echo $l_unlimited;?></td>
            <td><INPUT TYPE=TEXT NAME=dev_minedeflector_number SIZE=4 MAXLENGTH=10 VALUE=0 $onblur></td>
            <td>$l_beams</td>
            <td><input type=text readonly class='portcosts2' name=beams_costper VALUE='0' tabindex='-1' $onblur></td>
            <td><?php echo NUMBER($playerinfo->beams); ?></td>
            <td>
                <?php echo dropdown("beams_upgrade", $playerinfo->beams);?>
            </td>
        </TR>
        <TR>
            <td>$l_escape_pod</td>
            <td><?php echo NUMBER($dev_escapepod_price); ?></td>
            <?php if ($playerinfo->dev_escapepod == "N") : ?>
                <td>$l_none</td>
                <td>&nbsp;</td>
                <td><INPUT TYPE=CHECKBOX NAME=escapepod_purchase VALUE=1 $onclick></td>
            <?php else : ?>
                <td><?php echo $l_equipped;?></td>
                <td></td>
                <td><?php echo $l_n_a;?></td>
            <?php endif; ?>
            <td>$l_armor</td>
            <td><input type=text readonly class='portcosts1' name=armor_costper VALUE='0' tabindex='-1' $onblur></td>
            <td><?php echo NUMBER($playerinfo->armor); ?></td>
            <td>
                <?php echo dropdown("armor_upgrade", $playerinfo->armor);?>
            </td>
        </TR>
        <TR>
            <td>$l_fuel_scoop</td>
            <td><?php echo NUMBER($dev_fuelscoop_price); ?></td>
            <?php if ($playerinfo->dev_fuelscoop == "N") : ?>
                <td>$l_none</td>
                <td>&nbsp;</td>
                <td><INPUT TYPE=CHECKBOX NAME=fuelscoop_purchase VALUE=1 $onclick></td>
            <?php else : ?>
                <td><?php echo $l_equipped;?></td>
                <td></td>
                <td><?php echo $l_n_a;?></td>
            <?php endif; ?>
            <td>$l_cloak</td>
            <td><input type=text readonly class='portcosts2' name=cloak_costper VALUE='0' tabindex='-1' $onblur $onfocus></td>
            <td><?php echo NUMBER($playerinfo->cloak); ?></td>
            <td>
                <?php echo dropdown("cloak_upgrade", $playerinfo->cloak);?>
            </td>
        </TR>
        <TR>
            <td>$l_lssd</td>
            <td><?php echo NUMBER($dev_lssd_price); ?></td>
            <?php if ($playerinfo->dev_lssd == "N") : ?>
                <td>$l_none</td>
                <td>&nbsp;</td>
                <td><INPUT TYPE=CHECKBOX NAME=lssd_purchase VALUE=1 $onclick></td>
            <?php else : ?>
                <td><?php echo $l_equipped;?></td>
                <td></td>
                <td><?php echo $l_n_a;?></td>
            <?php endif; ?>
            <td>$l_torp_launch</td>
            <td><input type=text readonly class='portcosts1' name=torp_launchers_costper VALUE='0' tabindex='-1' $onblur></td>
            <td><?php echo NUMBER($playerinfo->torp_launchers); ?></td>
            <td>
                <?php echo dropdown("torp_launchers_upgrade", $playerinfo->torp_launchers);?>
            </td>
        </TR>
        <TR >
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>$l_shields</td>
            <td><input type=text readonly class='portcosts2' name=shields_costper VALUE='0' tabindex='-1' $onblur></td>
            <td><?php echo NUMBER($playerinfo->shields); ?></td>
            <td>
                <?php echo dropdown("shields_upgrade", $playerinfo->shields);?>
            </td>
        </TR>
    </TABLE>
    <BR>
    <TABLE>
        <TR>
            <td><?php echo $l_item;?></td>
            <td><?php echo $l_cost;?></td>
            <td><?php echo $l_current;?></td>
            <td><?php echo $l_max;?></td>
            <td><?php echo $l_qty;?></td>
        </TR>
        <TR >
            <td><?php echo $l_fighters;?></td>
            <td><?php echo NUMBER($fighter_price); ?></td>
            <td><?php echo NUMBER($playerinfo->ship_fighters); ?> / <?php echo NUMBER($fighter_max); ?></td>
            <td>
                <?php if ($playerinfo->ship_fighters != $fighter_max) : ?>
                    <?php echo NUMBER($fighter_free); ?></td>
                <td><INPUT TYPE=TEXT NAME=fighter_number SIZE=6 MAXLENGTH=10 VALUE=0 $onblur>
                <?php else : ?>
                    0<td><input type=text readonly class='portcosts1' NAME=fighter_number MAXLENGTH=10 value="<?php echo $l_full;?>" $onblur tabindex='-1'>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td><?php echo $l_torps;?></td>
            <td><?php echo NUMBER($torpedo_price); ?></td>
            <td><?php echo NUMBER($playerinfo->torps); ?> / <?php echo NUMBER($torpedo_max); ?></td>
            <td>
                <?php if ($playerinfo->torps != $torpedo_max) : ?>
                <?php echo NUMBER($torpedo_free); ?></td>
                <td><INPUT TYPE=TEXT NAME=torpedo_number SIZE=6 MAXLENGTH=10 VALUE=0 $onblur>
                <?php else : ?>
                    0<td><input type=text readonly class='portcosts1' NAME=torpedo_number MAXLENGTH=10 value="<?php echo $l_full;?>" $onblur tabindex='-1'>
                <?php endif; ?>
            </td>
        </TR>
        <TR >
            <td><?php echo $l_armorpts;?></td>
            <td><?php echo NUMBER($armor_price); ?></td>
            <td><?php echo NUMBER($playerinfo->armor_pts); ?> / <?php echo NUMBER($armor_max); ?></td>
            <td>
                <?php if ($playerinfo->armor_pts != $armor_max) : ?>
                    <a href='#' onClick=\"MakeMax('armor_number', $armor_free);countTotal();return false;\"; $onblur><?php echo NUMBER($armor_free); ?></td>
                <td><INPUT TYPE=TEXT NAME=armor_number SIZE=6 MAXLENGTH=10 VALUE=0 $onblur>
                <?php else : ?>
                    0<td><input type=text readonly class='portcosts2' NAME=armor_number MAXLENGTH=10 value="<?php echo $l_full;?>" tabindex='-1' $onblur>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td><?php echo $l_colonists;?></td>
            <td><?php echo NUMBER($colonist_price); ?></td>
            <td><?php echo NUMBER($playerinfo->ship_colonists); ?> / <?php echo NUMBER($colonist_max); ?></td>
            <td>
                <?php if ($playerinfo->ship_colonists != $colonist_max) : ?>
                    <a href='#'><?php echo NUMBER($colonist_free); ?></td>
                <td><INPUT TYPE=TEXT NAME=colonist_number SIZE=6 MAXLENGTH=10 VALUE=0 $onblur>
                <?php else : ?>
                    0<td><input type=text readonly class='portcosts2' NAME=colonist_number MAXLENGTH=10 value="<?php echo $l_full;?>" tabindex='-1' $onblur>
                <?php endif; ?>
            </td>
        </TR>
    </TABLE>
    <TABLE>
        <TR>
            <td><INPUT TYPE=SUBMIT VALUE="<?php echo $l_buy;?>"></td>
            <TD ALIGN=RIGHT><?php echo $l_totalcost;?>: <INPUT TYPE=TEXT  NAME=total_cost SIZE=22 VALUE=0></td>
        </TR>
    </TABLE>
</FORM>
<?php echo $l_would_dump;?> <A HREF=dump.php><?php echo $l_here;?></A>.
<?php break; ?>
<?php case BNT\Sector\SectorPortTypeEnum::None: ?>
<?php default: ?>
    <?php echo $l_noport;?>
    <?php break; ?>
<?php endswitch; ?>
<BR/>
<?php TEXT_GOTOMAIN(); ?>
<?php include("footer.php"); ?>
