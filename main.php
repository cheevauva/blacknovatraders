<?
include("config.php");
loadlanguage($lang);

connectdb();

if (isNotAuthorized()) {
    die();
}

$playerinfo = ship();

if (!empty($playerinfo->cleared_defences)) {
    header('Location: ' . $playerinfo->cleared_defences);
}

if ($playerinfo->on_planet) {
    $currentPlanet = BNT\Planet\DAO\PlanetRetrieveByIdDAO::call($playerinfo->planet_id);

    if (!$currentPlanet) {
        $playerinfo->on_planet = false;
        shipSave($playerinfo);
    } else {
        header('Location: planet.php?planet_id=' . $currentPlanet->planet_id . '&ship_id=' . $playerinfo->ship_id);
    }
}

$sectorinfo = \BNT\Sector\DAO\SectorRetrieveByIdDAO::call($playerinfo->sector);
$links = \BNT\Link\DAO\LinkRetrieveManyBySectorDAO::call($playerinfo->sector);
$planetsInSector = \BNT\Planet\DAO\PlanetRetrieveManyBySectorDAO::call($playerinfo->sector);
$traderoutes = \BNT\Traderoute\DAO\TraderouteRetrieveManyByShipDAO::call($playerinfo);
$defencesInSector = \BNT\SectorDefence\DAO\SectorDefenceRetrieveManyBySectorDAO::call($playerinfo->sector);
$zoneinfo = \BNT\Zone\DAO\ZoneRetrieveByIdDAO::call($sectorinfo->zone_id);
$shipsInSector = \BNT\Ship\DAO\ShipRetrieveManyBySectorDAO::call($playerinfo->sector);

$shiptypes[0] = "tinyship.gif";
$shiptypes[1] = "smallship.gif";
$shiptypes[2] = "mediumship.gif";
$shiptypes[3] = "largeship.gif";
$shiptypes[4] = "hugeship.gif";

$planettypes[0] = "tinyplanet.gif";
$planettypes[1] = "smallplanet.gif";
$planettypes[2] = "mediumplanet.gif";
$planettypes[3] = "largeplanet.gif";
$planettypes[4] = "hugeplanet.gif";

$title = $l_main_title;

include("header.php");
?>

<table style="width: 900px;margin: 0 auto 0 auto;">
    <tr>
        <td>
            <a href="report.php"><?php echo htmlspecialchars($playerinfo->ship_name); ?></a> (<?php echo htmlspecialchars($playerinfo->character_name); ?>) [ <?php echo player_insignia_name($playerinfo); ?> ] 
        </td>
        <td>
        </td>
        <td align=right>
            <a  href="logout.php"><?php echo $l_logout ?></a>
        </td>
    </tr>
    <tr>
        <td>
<?php echo $l_turns_have; ?>
            <?php echo NUMBER($playerinfo->turns); ?>
        </td>
        <td align=center>
<?php echo $l_turns_used ?> <b><?php echo NUMBER($playerinfo->turns_used); ?></b>
        </td>
        <td align=right>
<?php echo $l_score ?> <b><?php echo NUMBER($playerinfo->score) ?>&nbsp;</b>
        </td>
    </tr>
    <tr>
        <td>
<?php echo $l_sector ?>: <b><?php echo $playerinfo->sector; ?></b>&nbsp;
        </td>
        <td align=center>
<?php echo $sectorinfo->beacon; ?>
            <?php $zoneinfo->zone_name = $zoneinfo->zone_id < 5 ? ($l_zname[$zoneinfo->zone_id] ?? $l_unnamed) : $zoneinfo->zone_name; ?>
        </td>
        <td align=right>
            <a href="<?php echo "zoneinfo.php?zone=$zoneinfo->zone_id"; ?>"><?php echo $zoneinfo->zone_name; ?></a>&nbsp;
        </td>
    </tr>
</table>

<table style="width: 900px;margin: 0 auto 0 auto;">
    <tr>
        <td valign=top>
            <table>
                <tr>
                    <td>
<?php echo $l_commands ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <a href="device.php"><?php echo $l_devices ?></a>&nbsp;<br>
                            <a href="planet_report.php"><?php echo $l_planets ?></a>&nbsp;<br>
                            <a href="log.php"><?php echo $l_log ?></a>&nbsp;<br>
                            <a href="defence_report.php"><?php echo $l_sector_def ?></a>&nbsp;<br>
                            <a href="readmail.php"><?php echo $l_read_msg ?></A>&nbsp;<br>
                            <a href="mailto2.php"><?php echo $l_send_msg ?></a>&nbsp;<br>
                            <a href="ranking.php"><?php echo $l_rankings ?></a>&nbsp;<br>
                            <a href="settings.php">Settings</a>&nbsp;<br>
                            <a href="teams.php"><?php echo $l_teams ?></a>&nbsp;<br>
                            <a href="self_destruct.php"><?php echo $l_ohno ?></a>&nbsp;<br>
                            <a href="options.php"><?php echo $l_options ?></a>&nbsp;<br>
                            <a href="navcomp.php"><?php echo $l_navcomp ?></a>&nbsp;<br>
<?php if (!empty($ksm_allowed)): ?>
                                <a href="galaxy.php"><?php echo $l_map; ?></a>&nbsp;<br>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
<?php echo $l_information ?? '@todo'; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <a href="help.php"><?php echo $l_help; ?></a>&nbsp;<br>
                            <a href="faq.html"><?php echo $l_faq ?></a>&nbsp;<br>
                            <a href="feedback.php"><?php echo $l_feedback ?></a>&nbsp;<br>
<?php if (!empty($link_forums)): ?>
                                <a href="<?php echo $link_forums; ?>" TARGET="_blank"><?php echo $l_forums; ?></a>&nbsp;<br>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td >
<?php echo $l_traderoutes ?>

                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
<?php if (empty($traderoutes)) : ?>
                                <a >&nbsp;<?php echo $l_none; ?> &nbsp;</a>
                            <?php else: ?>
                                <?php foreach ($traderoutes as $traderoute) : ?>
                                    <?php $traderoute = asTraderoute($traderoute); ?>
                                    <a href="traderoute.php?engage=<?php echo $traderoute->traderoute_id; ?>">
                                    <?php echo getTraderouteSrcLabel($traderoute); ?>
                                        &nbsp; <?php echo getTraderouteDirectionLabel($traderoute); ?> &nbsp;
                                        <?php echo getTraderouteDstLabel($traderoute); ?>
                                    </a>&nbsp;
                                    <br>
    <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td nowrap>
                        <div >
                            &nbsp;<a  href=traderoute.php><?php echo $l_trade_control ?></a>&nbsp;<br>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
        <td style="min-width: 60%;text-align: left;vertical-align: top;">
            <table>
                <tr>
                    <td><?php echo $l_tradingport ?>:</td>
                </tr>
                <tr>
                    <td>
<?php if ($sectorinfo->port_type != "none") : ?>
                            <a href=port.php><?php echo ucfirst(t_port($sectorinfo->port_type)); ?></a>
                        <?php else : ?>
                            <?php echo $l_none; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo sprintf('%s [%s]', $l_planet_in_sec, $sectorinfo->sector_id); ?></td>
                </tr>
                <tr>
                    <td>
                        <table border=0 width="100%">
<?php if (empty($planetsInSector)): ?>
                                <tr>
                                    <td>
    <?php echo $l_none; ?>
                                    </td>
                                </tr>
<?php else: ?>
                                <?php foreach ($planetsInSector ?: [] as $planet) : ?>
                                    <?php $planet = asPlanet($planet); ?>
                                    <?php $planet_owner = \BNT\Ship\DAO\ShipRetrieveByIdDAO::call($planet->owner); ?>
                                    <tr>
                                        <td>
                                            <a href="planet.php?planet_id=<?php echo $planet->planet_id; ?>">
                                                <img src="images/<?php echo $planettypes[getPlanetLevel($planet_owner)]; ?>" border=0>
                                            </a>
                                            <br/>

        <?php echo ($planet->name ?: $l_unnamed); ?><br>
                                            <?php echo $planet->owner ? $planet_owner->character_name : $l_unowned; ?> 
                                        </td>
                                    </tr>
    <?php endforeach; ?>
                            <?php endif; ?>


                        </table>
                    </td>
                </tr>
                <tr>
                    <td><?php echo sprintf('%s [%s]', $l_ships_in_sec, $sectorinfo->sector_id); ?></td>
                </tr>
                <tr>
                    <td>
                        <table border=0 width="100%">
<?php if (empty($shipsInSector)): ?>
                                <tr>
                                    <td>
    <?php echo $l_sector_0; ?>
                                    </td>
                                </tr>
<?php else: ?>
                                <?php foreach ($shipsInSector as $shipInSector): ?>
                                    <?php $shipInSector = asShip($shipInSector); ?>
                                    <tr>
                                        <td>
        <?php
        $success = SCAN_SUCCESS($playerinfo->sensors, $ship->cloak);
        $success = $success < 5 ? 5 : $success;
        $success = $success > 95 ? 95 : $success;
        $roll = rand(1, 100);
        ?>
                                            <?php if ($roll < $success): ?>

                                                <a href="ship.php?ship_id=<?php echo $shipInSector->ship_id; ?>">
                                                    <img src="images/<?php echo $shiptypes[$shipInSector->getLevel()]; ?>" border=0>
                                                </a>
                                                <br>
            <?php echo $shipInSector->ship_name; ?><br>
                                                (<?php echo $shipInSector->character_name; ?>)
                                                <?php if (!empty($team)): ?>&nbsp;(<?php echo $team->team_name; ?><?php endif; ?>

                                            <?php else: ?>
                                                <img src="images/<?php echo $shiptypes[$shipInSector->getLevel()]; ?>"><br>
                                                <?php echo $l_unknown; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
    <?php endforeach; ?>
                            <?php endif; ?>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td>
            <table border=0 width="100%">
                <tr>
                    <td><?php echo $l_sector_def; ?></td>
                </tr>
            </table>
            <table border=0 width="100%">
<?php if (empty($defencesInSector)): ?>
                    <tr>
                        <td><?php echo $l_none; ?></td>
                    </tr>
<?php else: ?>
                    <?php foreach ($defencesInSector as $defenceInSector): ?>
                        <?php $defenceInSector = asSectorDefence($defenceInSector); ?>
                        <?php $shipDefenceinSector = BNT\Ship\DAO\ShipRetrieveByIdDAO::call($defenceInSector->ship_id); ?>
                        <tr>
                            <td>
                                <a href="modify_defences.php?defence_id=<?php echo $defenceInSector->defence_id; ?>">
                                    <table>
                                        <tr>
                                            <td >
        <?php if ($defenceInSector->defence_type === BNT\SectorDefence\SectorDefenceTypeEnum::Mines): ?>
                                                    <img src="images/mines.gif">
                                                    <?php $def_type = $l_mines; ?>
                                                <?php endif; ?>
                                                <?php if ($defenceInSector->defence_type === BNT\SectorDefence\SectorDefenceTypeEnum::Fighters): ?>
                                                    <img src="images/fighters.gif">
                                                    <?php
                                                    $def_type = $l_fighters . match ($defenceInSector->fm_setting) {
                                                        BNT\SectorDefence\SectorDefenceFmSettingEnum::Attack => $l_md_attack,
                                                        BNT\SectorDefence\SectorDefenceFmSettingEnum::Toll => $l_md_toll,
                                                    };
                                                    ?>
                                                <?php endif; ?>

                                            </td>
                                            <td><?php echo $defenceInSector->quantity; ?> <?php echo $def_type; ?></td>
                                        </tr>
                                        <tr colspan="2">
                                            <td>

        <?php echo $shipDefenceinSector->character_name; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </a>
                            </td>
                        </tr>
    <?php endforeach; ?>
                <?php endif; ?>
            </table>
            <table> 
                <tr>
                    <td>
                        <table> 
                            <tr>
                                <td colspan="2">
<?php echo $l_cargo ?>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;<img  src="images/ore.gif">&nbsp;<?php echo $l_ore ?>&nbsp;</td>
                                <td><span >&nbsp;<?php echo NUMBER($playerinfo->ship_ore); ?>&nbsp;</span></td>
                            </tr>
                            <tr>
                                <td>&nbsp;<img  src="images/organics.gif">&nbsp;<?php echo $l_organics ?>&nbsp;</td>
                                <td><span >&nbsp;<?php echo NUMBER($playerinfo->ship_organics); ?>&nbsp;</span></td>
                            </tr>
                            <tr>
                                <td>&nbsp;<img  src="images/goods.gif">&nbsp;<?php echo $l_goods ?>&nbsp;</td>
                                <td><span >&nbsp;<?php echo NUMBER($playerinfo->ship_goods); ?>&nbsp;</span></td>
                            </tr>
                            <tr>
                                <td>&nbsp;<img   src="images/energy.gif">&nbsp;<?php echo $l_energy ?>&nbsp;</td> 
                                <td><span >&nbsp;<?php echo NUMBER($playerinfo->ship_energy); ?>&nbsp;</span></td>
                            </tr>
                            <tr>
                                <td>&nbsp;<img  src="images/colonists.gif">&nbsp;<?php echo $l_colonists ?>&nbsp;</td> 
                                <td><span >&nbsp;<?php echo NUMBER($playerinfo->ship_colonists); ?>&nbsp;</span></td>
                            </tr>
                            <tr>
                                <td>&nbsp;<img src="images/credits.gif">&nbsp;<?php echo $l_credits ?>&nbsp;</td>
                                <td><span >&nbsp;<?php echo NUMBER($playerinfo->credits); ?>&nbsp;</span></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>
<?php echo $l_realspace ?>

                    </td>
                </tr>
                <tr>
                    <td>
                        <div >
                            &nbsp;<a  href="rsmove.php?engage=1&amp;destination=<?php echo $playerinfo->preset1; ?>">=&gt;&nbsp;<?php echo $playerinfo->preset1; ?></a>&nbsp;<a  href=preset.php>[<?php echo $l_set ?>]</a>&nbsp;<br>
                            &nbsp;<a  href="rsmove.php?engage=1&amp;destination=<?php echo $playerinfo->preset2; ?>">=&gt;&nbsp;<?php echo $playerinfo->preset2; ?></a>&nbsp;<a  href=preset.php>[<?php echo $l_set ?>]</a>&nbsp;<br>
                            &nbsp;<a  href="rsmove.php?engage=1&amp;destination=<?php echo $playerinfo->preset3; ?>">=&gt;&nbsp;<?php echo $playerinfo->preset3; ?></a>&nbsp;<a  href=preset.php>[<?php echo $l_set ?>]</a>&nbsp;<br>
                            &nbsp;<a  href="rsmove.php">=&gt;&nbsp;<?php echo $l_main_other; ?></a>&nbsp;<br>
                        </div>
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>
<?php echo $l_main_warpto; ?>
                    </td>
                </tr>
                <tr>
                    <TD NOWRAP>
                        <div >
<?php if (empty($links)): ?>
                                &nbsp;<a ><?php echo $l_no_warplink; ?></a>&nbsp;<br>
                            <?php else: ?>
                                <?php foreach ($links as $link): ?>
                                    <?php assert($link instanceof \BNT\Link\Link); ?>
                                    <a  href="move.php?sector=<?php echo $link->link_dest; ?>">
                                        =&gt;&nbsp;<?php echo $link->link_dest; ?>
                                    </a>&nbsp;
                                    <a  href="lrscan.php?sector=<?php echo $link->link_dest; ?>">
                                        [<?php echo $l_scan; ?>]
                                    </a>&nbsp;<br>
    <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td nowrap align=center>
                        <div >
                            &nbsp;<a  href="lrscan.php?sector=*">[<?php echo $l_fullscan; ?>]</a>&nbsp;<br>
                        </div>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
<?php include("footer.php"); ?>
