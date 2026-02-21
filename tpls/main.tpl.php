<?php
$title = $l->main_title;
$self = BNT\Controller\MainController::as($this);
$picsperrow = 7;
$shiptypes = shipTypes();
$planettypes = planetTypes();
?>
<?php include("header.php"); ?>

<table class="table">
    <tr>
        <td align="center" colspan=3>
            <a href="ships.php"><?php echo htmlspecialchars($self->playerinfo['ship_name']); ?></a> [<?php echo player_insignia_name($self->playerinfo['ship_id']); ?>]
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;<?php echo $l->turns_have; ?><?php echo NUMBER($self->playerinfo['turns']) ?>
        </td>
        <td align=center>
            <?php echo $l->turns_used ?><?php echo NUMBER($self->playerinfo['turns_used']); ?>
        </td>
        <td align=right>
            <?php echo $l->score ?><?php echo NUMBER($self->playerinfo['score']) ?>&nbsp;
        </td>
    <tr>
        <td>
            &nbsp;<?php echo $l->sector ?>: <?php echo $self->playerinfo['sector']; ?>
        </td>
        <td align=center>
            <?php if (!empty($self->sectorinfo['beacon'])) : ?>
                <?php echo $self->sectorinfo['beacon']; ?>
            <?php endif; ?>

            <?php if ($self->zoneinfo['zone_id'] < 5) : ?>
                <?php $self->zoneinfo['zone_name'] = $l->zname[$self->zoneinfo['zone_id']]; ?>
            <?php endif; ?>
        </td>
        <td align=right>
            <a href="zoneinfo.php?zone=<?php echo $self->zoneinfo['zone_id']; ?>"><?php echo htmlspecialchars($self->zoneinfo['zone_name']); ?></a>
        </td>
    </tr>
    <tr>
        <td valign=top width="20%">
            <TABLE>
                <tr>
                    <td><?php echo $l->commands ?></td>
                </tr>
                <TR>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item"><a class="nav-link" href="device.php"><?php echo $l->devices ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="planet_report.php"><?php echo $l->planets ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="log.php"><?php echo $l->log ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="defence_report.php"><?php echo $l->sector_def ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="readmail.php"><?php echo $l->read_msg ?></A></li>
                            <li class="list-group-item"><a class="nav-link" href="mailto2.php"><?php echo $l->send_msg ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="teams.php"><?php echo $l->teams ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="self_destruct.php"><?php echo $l->ohno ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="options.php"><?php echo $l->options ?></a></li>
                            <?php if (!empty($ksm_allowed)) : ?>
                                <li class="list-group-item"><a class="nav-link" href="galaxy.php"><?php echo $l->map ?></a></li>
                            <?php endif; ?>
                            <li class="list-group-item"><a class="nav-link" href="navcomp.php"><?php echo $l->navcomp ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="logout.php"><?php echo $l->logout ?></a></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $l->traderoutes ?></td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group">
                            <?php if (empty($self->traderoutes)) : ?>
                                <li class="list-group-item">
                                    <?php echo $l->none; ?>
                                </li>
                            <?php endif; ?>
                            <?php foreach ($self->traderoutes as $traderoute) : ?>
                                <li class="list-group-item">
                                    <a href="traderoute.php?engage=<?php echo $traderoute['traderoute_id']; ?>">
                                        <?php if ($traderoute['source_type'] == 'P') : ?>
                                            <?php echo $l->port; ?>
                                        <?php elseif ($traderoute['source_type'] == 'D') : ?>
                                            <?php echo "Def's "; ?>
                                        <?php else : ?>
                                            <?php echo empty($traderoute['planet_source']) ? $l->unnamed : $traderoute['planet_source']; ?>
                                        <?php endif; ?>

                                        <?php if ($traderoute['circuit'] == '1') :
                                            ?> =&gt;&nbsp;<?php
                                        else :
                                            ?>&lt;=&gt;&nbsp;<?php endif; ?>

                                        <?php if ($traderoute['dest_type'] == 'P') : ?>
                                            <?php echo $traderoute['dest_id']; ?>
                                        <?php elseif ($traderoute['dest_type'] == 'D') : ?>
                                            <?php echo "Def's in " . $traderoute['dest_id'] . ""; ?>
                                        <?php else : ?>
                                            <?php echo empty($traderoute['planet_dest']) ? $l->unnamed : $traderoute['planet_dest']; ?>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>

                            <li class="list-group-item">
                                <a href=traderoute.php><?php echo $l->trade_control ?></a>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
        <td valign=top width="60%">
            <?php echo $l->tradingport ?>:&nbsp;
            <?php if ($self->sectorinfo['port_type'] != "none") : ?>
                <a href=port.php><?php echo ucfirst(t_port($self->sectorinfo['port_type'])); ?></a>
            <?php else : ?>
                <?php echo $l->none; ?>
            <?php endif; ?>
            <br/>
            <?php echo $l->planet_in_sec; ?>:
            <table class="table">
                <?php if (empty($self->planets)) : ?>
                    <tr>
                        <td align=center valign=top>
                            <?php echo $l->none; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach (array_chunk($self->planets, $picsperrow) as $self->planetsOnRow) : ?>
                    <tr>
                        <?php foreach ($self->planetsOnRow as $planet) : ?>
                            <td align=center valign=top>
                                <A HREF="planet.php?planet_id=<?php echo $planet['planet_id']; ?>">
                                    <img src="images/<?php echo $planettypes[planetLevel($planet['owner_score'])]; ?>" border=0>
                                </a>
                                <br>
                                <?php echo empty($planet['name']) ? $l->unnamed : htmlspecialchars($planet['name']); ?>
                                <br>
                                (<?php echo empty($planet['owner']) ? $l->unowned : htmlspecialchars($planet['owner_ship_name']); ?>)
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php echo $l->ships_in_sec ?>:
            <table class="table">
                <?php if (empty($self->playerinfo['sector'])) : ?>
                    <tr>
                        <td align=center valign=top>
                            <?php echo $l->sector_0; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if (!empty($self->playerinfo['sector']) && empty($self->shipsInSector)) : ?>
                    <tr>
                        <td align=center valign=top>
                            <?php echo $l->none; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach (array_chunk($self->shipsInSector, $picsperrow) as $shipsOnRow) : ?>
                    <tr>
                        <?php foreach ($shipsOnRow as $shipInSector) : ?>
                            <td align=center valign=top>
                                <a href="ship.php?ship_id=<?php echo $shipInSector['ship_id']; ?>">
                                    <img src="images/<?php echo $shiptypes[shipLevel($shipInSector['score'])]; ?>" border=0>
                                </a>
                                <?php echo htmlspecialchars($shipInSector['ship_name']); ?> (<?php echo htmlspecialchars($shipInSector['ship_name']); ?>)
                                <?php if (!empty($shipInSector['team_name'])) :
                                    ?>&nbsp;(<?php echo htmlspecialchars($shipInSector['team_name']); ?>)<?php endif;
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php echo $l->sector_def; ?>:
            <table class="table">
                <?php if (empty($self->defences)) : ?>
                    <tr>
                        <td align=center>
                            <?php echo $l->none; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach (array_chunk($self->defences, $picsperrow) as $self->defencesInSector) : ?>
                    <tr>
                        <?php foreach ($self->defencesInSector as $defence) : ?>
                            <td align=center valign=top>
                                <?php if ($defence['defence_type'] == 'F') : ?>
                                    <a href="modify_defences.php?defence_id=<?php echo $defence['defence_id']; ?>">
                                        <img src="images/fighters.gif" border=0>
                                    </a>
                                    <?php $def_type = $l->fighters; ?>
                                    <?php $def_type .= ($defence['fm_setting'] == 'attack') ? $l->md_attack : $l->md_toll; ?>
                                <?php endif; ?>
                                <?php if ($defence['defence_type'] == 'M') : ?>
                                    <a href="modify_defences.php?defence_id=<?php echo $defence['defence_id']; ?>">
                                        <img src="images/mines.gif" border=0>
                                    </a>
                                    <?php $def_type = $l->mines; ?>
                                <?php endif; ?>
                                <BR>
                                <?php echo $defence['ship_name']; ?><br/>
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
                    <td><?php echo $l->cargo ?></td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <img alt="<?php echo $l->ore ?>" src="images/ore.gif">&nbsp;<?php echo $l->ore ?>:
                                <span class="float-end"><?php echo NUMBER($self->playerinfo['ship_ore']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?php echo $l->organics ?>" src="images/organics.gif">&nbsp;<?php echo $l->organics ?>:
                                <span class="float-end"><?php echo NUMBER($self->playerinfo['ship_organics']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?php echo $l->goods ?>" src="images/goods.gif">&nbsp;<?php echo $l->goods ?>:
                                <span class="float-end"><?php echo NUMBER($self->playerinfo['ship_goods']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?php echo $l->energy ?>" src="images/energy.gif">&nbsp;<?php echo $l->energy ?>:
                                <span class="float-end"><?php echo NUMBER($self->playerinfo['ship_energy']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?php echo $l->colonists ?>" src="images/colonists.gif">&nbsp;<?php echo $l->colonists ?>:
                                <span class="float-end"><?php echo NUMBER($self->playerinfo['ship_colonists']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?php echo $l->credits ?>" src="images/credits.gif">&nbsp;<?php echo $l->credits ?>:
                                <span class="float-end"><?php echo NUMBER($self->playerinfo['credits']); ?></span>
                            </li>
                        </ul>

                    </td>
                </tr>
                <tr>
                    <td><?php echo $l->realspace ?></td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <a class="nav-link d-inline" href="rsmove.php?engage=1&amp;destination=<?php echo $self->playerinfo['preset1']; ?>">
                                    =&gt;&nbsp;<?php echo $self->playerinfo['preset1']; ?>
                                </a>
                                <a class="nav-link d-inline ms-2" href="preset.php">[<?php echo $l->set; ?>]</a>
                            </li>
                            <li class="list-group-item">
                                <a class="nav-link d-inline" href="rsmove.php?engage=1&amp;destination=<?php echo $self->playerinfo['preset2']; ?>">
                                    =&gt;&nbsp;<?php echo $self->playerinfo['preset2']; ?>
                                </a>
                                <a class="nav-link d-inline ms-2" href="preset.php">[<?php echo $l->set; ?>]</a>
                            </li>
                            <li class="list-group-item">
                                <a class="nav-link d-inline" href="rsmove.php?engage=1&amp;destination=<?php echo $self->playerinfo['preset3']; ?>">
                                    =&gt;&nbsp;<?php echo $self->playerinfo['preset3']; ?>
                                </a>
                                <a class="nav-link d-inline ms-2" href="preset.php">[<?php echo $l->set; ?>]</a>
                            </li>
                            <li class="list-group-item">
                                <a class="nav-link" href="rsmove.php">=&gt;&nbsp;<?php echo $l->main_other; ?></a>
                            </li>
                        </ul>
                    </td>
                </tr> 
                <tr>
                    <td>
                        <?php echo $l->main_warpto; ?>
                    </td>
                </tr>
                <tr>
                    <TD>
                        <ul class="list-group">
                            <?php if (empty($self->links)) : ?>
                                <li class="list-group-item">
                                    <a class=dis><?php echo $l->no_warplink; ?></a>
                                </li>
                            <?php else : ?>
                                <?php foreach ($self->links as $link) : ?>
                                    <li class="list-group-item">
                                        <a href="move.php?sector=<?php echo $link['link_dest']; ?>">=&gt;&nbsp;<?php echo $link['link_dest']; ?></a>&nbsp;
                                        <a href="lrscan.php?sector=<?php echo $link['link_dest']; ?>"><?php echo $l->scan; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li class="list-group-item">
                                <a class=dis><a class=dis href="lrscan.php?sector=*"><?php echo $l->fullscan; ?></a>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php include("footer.php"); ?>
