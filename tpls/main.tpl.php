<?php $title = $l_main_title; ?>
<?php
$picsperrow = 7;
?>
<?php include("header.php"); ?>

<table class="table">
    <tr>
        <td align="center" colspan=3>
            <?php echo player_insignia_name($username); ?> <?php echo htmlspecialchars($playerinfo['character_name']); ?> <?php echo $l_abord ?> <a href="report.php"><?php echo htmlspecialchars($playerinfo['ship_name']); ?>
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;<?php echo $l_turns_have; ?><?php echo NUMBER($playerinfo['turns']) ?>
        </td>
        <td align=center>
            <?php echo $l_turns_used ?><?php echo NUMBER($playerinfo['turns_used']); ?>
        </td>
        <td align=right>
            <?php echo $l_score ?><?php echo NUMBER($playerinfo['score']) ?>&nbsp;
        </td>
    <tr>
        <td>
            &nbsp;<?php echo $l_sector ?>: <?php echo $playerinfo['sector']; ?>
        </td>
        <td align=center>
            <?php if (!empty($sectorinfo['beacon'])) : ?>
                <?php echo $sectorinfo['beacon']; ?>
            <?php endif; ?>

            <?php if ($zoneinfo['zone_id'] < 5) : ?>
                <?php $zoneinfo['zone_name'] = $l_zname[$zoneinfo['zone_id']]; ?>
            <?php endif; ?>
        </td>
        <td align=right>
            <a href="zoneinfo.php?zone=<?php echo $zoneinfo['zone_id']; ?>"><?php echo htmlspecialchars($zoneinfo['zone_name']); ?></a>
        </td>
    </tr>
    <tr>
        <td valign=top width="20%">
            <TABLE>
                <tr>
                    <td><?php echo $l_commands ?></td>
                </tr>
                <TR>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item"><a class="nav-link" href="device.php"><?php echo $l_devices ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="planet_report.php"><?php echo $l_planets ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="log.php"><?php echo $l_log ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="defence_report.php"><?php echo $l_sector_def ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="readmail.php"><?php echo $l_read_msg ?></A></li>
                            <li class="list-group-item"><a class="nav-link" href="mailto2.php"><?php echo $l_send_msg ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="teams.php"><?php echo $l_teams ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="self_destruct.php"><?php echo $l_ohno ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="options.php"><?php echo $l_options ?></a></li>
                            <?php if (!empty($ksm_allowed)) : ?>
                                <li class="list-group-item"><a class="nav-link" href="galaxy.php"><?php echo $l_map ?></a></li>
                            <?php endif; ?>
                            <li class="list-group-item"><a class="nav-link" href="navcomp.php"><?php echo $l_navcomp ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="feedback.php"><?php echo $l_feedback ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="logout.php"><?php echo $l_logout ?></a></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l_traderoutes ?></td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group">
                            <?php if (empty($traderoutes)) : ?>
                                <li class="list-group-item">
                                    <?php echo $l_none; ?>
                                </li>
                            <?php endif; ?>
                            <?php foreach ($traderoutes as $traderoute) : ?>
                                <li class="list-group-item">
                                    <a href="traderoute.php?engage=<?php echo $traderoute['traderoute_id']; ?>">
                                        <?php if ($traderoute['source_type'] == 'P') : ?>
                                            <?php echo $l_port; ?>
                                        <?php elseif ($traderoute['source_type'] == 'D') : ?>
                                            <?php echo "Def's "; ?>
                                        <?php else : ?>
                                            <?php echo empty($traderoute['planet_source']) ? $l_unnamed : $traderoute['planet_source']; ?>
                                        <?php endif; ?>

                                        <?php if ($traderoute['circuit'] == '1') :
                                            ?> =&gt;&nbsp;<?php
                                        else :
                                            ?>&lt;=&gt;&nbsp;<?php
                                        endif; ?>

                                        <?php if ($traderoute['dest_type'] == 'P') : ?>
                                            <?php echo $traderoute['dest_id']; ?>
                                        <?php elseif ($traderoute['dest_type'] == 'D') : ?>
                                            <?php echo "Def's in " . $traderoute['dest_id'] . ""; ?>
                                        <?php else : ?>
                                            <?php echo empty($traderoute['planet_dest']) ? $l_unnamed : $traderoute['planet_dest']; ?>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>

                            <li class="list-group-item">
                                <a href=traderoute.php><?php echo $l_trade_control ?></a>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
        <td valign=top width="60%">
            <?php echo $l_tradingport ?>:&nbsp;
            <?php if ($sectorinfo['port_type'] != "none") : ?>
                <a href=port.php><?php echo ucfirst(t_port($sectorinfo['port_type'])); ?></a>
            <?php else : ?>
                <?php echo $l_none; ?>
            <?php endif; ?>
            <br/>
            <?php echo $l_planet_in_sec; ?>:
            <table class="table">
                <?php if (empty($planets)) : ?>
                    <tr>
                        <td align=center valign=top>
                            <?php echo $l_none; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach (array_chunk($planets, $picsperrow) as $planetsOnRow) : ?>
                    <tr>
                        <?php foreach ($planetsOnRow as $planet) : ?>
                            <td align=center valign=top>
                                <A HREF="planet.php?planet_id=<?php echo $planet['planet_id']; ?>">
                                    <img src="images/<?php echo $planettypes[planetLevel($planet['owner_score'])]; ?>" border=0>
                                </a>
                                <br>
                                <?php echo empty($planet['name']) ? $l_unnamed : htmlspecialchars($planet['name']); ?>
                                <br>
                                (<?php echo empty($planet['owner']) ? $l_unowned : htmlspecialchars($planet['owner_character_name']); ?>)
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php echo $l_ships_in_sec ?>:
            <table class="table">
                <?php if (empty($playerinfo['sector'])) : ?>
                    <tr>
                        <td align=center valign=top>
                            <?php echo $l_sector_0; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if (!empty($playerinfo['sector']) && empty($shipsInSector)) : ?>
                    <tr>
                        <td align=center valign=top>
                            <?php echo $l_none; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach (array_chunk($shipsInSector, $picsperrow) as $shipsOnRow) : ?>
                    <tr>
                        <?php foreach ($shipsOnRow as $shipInSector) : ?>
                            <td align=center valign=top>
                                <a href="ship.php?ship_id=<?php echo $shipInSector['ship_id']; ?>">
                                    <img src="images/<?php echo $shiptypes[shipLevel($shipInSector['score'])]; ?>" border=0>
                                </a>
                                <?php echo htmlspecialchars($shipInSector['ship_name']); ?> (<?php echo htmlspecialchars($shipInSector['character_name']); ?>)
                                <?php if (!empty($shipInSector['team_name'])) :
                                    ?>&nbsp;(<?php echo htmlspecialchars($shipInSector['team_name']); ?>)<?php
                                endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php echo $l_sector_def; ?>:
            <table class="table">
                <?php if (empty($defences)) : ?>
                    <tr>
                        <td align=center>
                            <?php echo $l_none; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach (array_chunk($defences, $picsperrow) as $defencesInSector) : ?>
                    <tr>
                        <?php foreach ($defencesInSector as $defence) : ?>
                            <td align=center valign=top>
                                <?php if ($defence['defence_type'] == 'F') : ?>
                                    <a href="modify_defences.php?defence_id=<?php echo $defence['defence_id']; ?>">
                                        <img src="images/fighters.gif" border=0>
                                    </a>
                                    <?php $def_type = $l_fighters; ?>
                                    <?php $def_type .= ($defence['fm_setting'] == 'attack') ? $l_md_attack : $l_md_toll; ?>
                                <?php endif; ?>
                                <?php if ($defence['defence_type'] == 'M') : ?>
                                    <a href="modify_defences.php?defence_id=<?php echo $defence['defence_id']; ?>">
                                        <img src="images/mines.gif" border=0>
                                    </a>
                                    <?php $def_type = $l_mines; ?>
                                <?php endif; ?>
                                <BR>
                                <?php echo $defence['character_name']; ?><br/>
                                <?php echo $defence['quantity']; ?> <?php echo $def_type; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </td>
        <td valign=top align="right" width="20%">
            <table> 
                <tr>
                    <td><?php echo $l_cargo ?></td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <img alt="<?php echo $l_ore ?>" src="images/ore.gif">&nbsp;<?php echo $l_ore ?>:
                                <span class="float-end"><?php echo NUMBER($playerinfo['ship_ore']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?php echo $l_organics ?>" src="images/organics.gif">&nbsp;<?php echo $l_organics ?>:
                                <span class="float-end"><?php echo NUMBER($playerinfo['ship_organics']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?php echo $l_goods ?>" src="images/goods.gif">&nbsp;<?php echo $l_goods ?>:
                                <span class="float-end"><?php echo NUMBER($playerinfo['ship_goods']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?php echo $l_energy ?>" src="images/energy.gif">&nbsp;<?php echo $l_energy ?>:
                                <span class="float-end"><?php echo NUMBER($playerinfo['ship_energy']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?php echo $l_colonists ?>" src="images/colonists.gif">&nbsp;<?php echo $l_colonists ?>:
                                <span class="float-end"><?php echo NUMBER($playerinfo['ship_colonists']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?php echo $l_credits ?>" src="images/credits.gif">&nbsp;<?php echo $l_credits ?>:
                                <span class="float-end"><?php echo NUMBER($playerinfo['credits']); ?></span>
                            </li>
                        </ul>

                    </td>
                </tr>
                <tr>
                    <td><?php echo $l_realspace ?></td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <a class="nav-link d-inline" href="rsmove.php?engage=1&amp;destination=<?php echo $playerinfo['preset1']; ?>">
                                    =&gt;&nbsp;<?php echo $playerinfo['preset1']; ?>
                                </a>
                                <a class="nav-link d-inline ms-2" href="preset.php">[<?php echo $l_set; ?>]</a>
                            </li>
                            <li class="list-group-item">
                                <a class="nav-link d-inline" href="rsmove.php?engage=1&amp;destination=<?php echo $playerinfo['preset2']; ?>">
                                    =&gt;&nbsp;<?php echo $playerinfo['preset2']; ?>
                                </a>
                                <a class="nav-link d-inline ms-2" href="preset.php">[<?php echo $l_set; ?>]</a>
                            </li>
                            <li class="list-group-item">
                                <a class="nav-link d-inline" href="rsmove.php?engage=1&amp;destination=<?php echo $playerinfo['preset3']; ?>">
                                    =&gt;&nbsp;<?php echo $playerinfo['preset3']; ?>
                                </a>
                                <a class="nav-link d-inline ms-2" href="preset.php">[<?php echo $l_set; ?>]</a>
                            </li>
                            <li class="list-group-item">
                                <a class="nav-link" href="rsmove.php">=&gt;&nbsp;<?php echo $l_main_other; ?></a>
                            </li>
                        </ul>
                    </td>
                </tr> 
                <tr>
                    <td>
                        <?php echo $l_main_warpto; ?>
                    </td>
                </tr>
                <tr>
                    <TD>
                        <ul class="list-group">
                            <?php if (empty($links)) : ?>
                                <li class="list-group-item">
                                    <a class=dis><?php echo $l_no_warplink; ?></a>
                                </li>
                            <?php else : ?>
                                <?php foreach ($links as $link) : ?>
                                    <li class="list-group-item">
                                        <a href="move.php?sector=<?php echo $link['link_dest']; ?>">=&gt;&nbsp;<?php echo $link['link_dest']; ?></a>&nbsp;
                                        <a href="lrscan.php?sector=<?php echo $link['link_dest']; ?>"><?php echo $l_scan; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li class="list-group-item">
                                <a class=dis><a class=dis href="lrscan.php?sector=*"><?php echo $l_fullscan; ?></a>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php include("footer.php"); ?>
