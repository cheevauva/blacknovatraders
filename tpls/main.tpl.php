<?php
$self = BNT\Controller\MainController::as($self);
$picsperrow = 7;
$shiptypes = shipTypes();
$planettypes = planetTypes();
?>
<?php include("header.php"); ?>

<table class="table">
    <tr>
        <td align="center" colspan=3>
            <a href="<?= route('ships'); ?>"><?= htmlspecialchars($self->playerinfo['ship_name']); ?></a> [<?= player_insignia_name($self->playerinfo['ship_id']); ?>]
        </td>
    </tr>
    <tr>
        <td>
            &nbsp;<?= $l->turns_have; ?><?= NUMBER($self->playerinfo['turns']) ?>
        </td>
        <td align=center>
            <?= $l->turns_used ?><?= NUMBER($self->playerinfo['turns_used']); ?>
        </td>
        <td align=right>
            <?= $l->score ?><?= NUMBER($self->playerinfo['score']) ?>&nbsp;
        </td>
    <tr>
        <td>
            &nbsp;<?= $l->sector ?>: <?= $self->playerinfo['sector']; ?>
        </td>
        <td align=center>
            <?php if (!empty($self->sectorinfo['beacon'])) : ?>
                <?= $self->sectorinfo['beacon']; ?>
            <?php endif; ?>

            <?php if ($self->zoneinfo['zone_id'] < 5) : ?>
                <?php $self->zoneinfo['zone_name'] = $l->zname[$self->zoneinfo['zone_id']]; ?>
            <?php endif; ?>
        </td>
        <td align=right>
            <a href="<?= route('zoneinfo', ['zone' => $self->zoneinfo['zone_id']]); ?>"><?= htmlspecialchars($self->zoneinfo['zone_name']); ?></a>
        </td>
    </tr>
    <tr>
        <td valign=top width="20%">
            <TABLE>
                <tr>
                    <td><?= $l->commands ?></td>
                </tr>
                <TR>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item"><a class="nav-link" href="<?= route('device'); ?>"><?= $l->devices ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('planet_report.php'); ?>"><?= $l->planets ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('log'); ?>"><?= $l->log ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('defence_report.php'); ?>"><?= $l->sector_def ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('readmail.php'); ?>"><?= $l->read_msg ?></A></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('mailto2.php'); ?>"><?= $l->send_msg ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('teams.php'); ?>"><?= $l->teams ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('self_destruct'); ?>"><?= $l->ohno ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('options'); ?>"><?= $l->options ?></a></li>
                            <?php if (!empty($ksm_allowed)) : ?>
                                <li class="list-group-item"><a class="nav-link" href="galaxy.php"><?= $l->map ?></a></li>
                            <?php endif; ?>
                            <li class="list-group-item"><a class="nav-link" href="navcomp.php"><?= $l->navcomp ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('logout'); ?>"><?= $l->logout ?></a></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td><?= $l->traderoutes ?></td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group">
                            <?php if (empty($self->traderoutes)) : ?>
                                <li class="list-group-item">
                                    <?= $l->none; ?>
                                </li>
                            <?php endif; ?>
                            <?php foreach ($self->traderoutes as $traderoute) : ?>
                                <li class="list-group-item">
                                    <a href="traderoute.php?engage=<?= $traderoute['traderoute_id']; ?>">
                                        <?php if ($traderoute['source_type'] == 'P') : ?>
                                            <?= $l->port; ?>
                                        <?php elseif ($traderoute['source_type'] == 'D') : ?>
                                            <?= "Def's "; ?>
                                        <?php else : ?>
                                            <?= empty($traderoute['planet_source']) ? $l->unnamed : $traderoute['planet_source']; ?>
                                        <?php endif; ?>

                                        <?php if ($traderoute['circuit'] == '1') :
                                            ?> =&gt;&nbsp;<?php
                                        else :
                                            ?>&lt;=&gt;&nbsp;<?php endif; ?>

                                        <?php if ($traderoute['dest_type'] == 'P') : ?>
                                            <?= $traderoute['dest_id']; ?>
                                        <?php elseif ($traderoute['dest_type'] == 'D') : ?>
                                            <?= "Def's in " . $traderoute['dest_id'] . ""; ?>
                                        <?php else : ?>
                                            <?= empty($traderoute['planet_dest']) ? $l->unnamed : $traderoute['planet_dest']; ?>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>

                            <li class="list-group-item">
                                <a href=traderoute.php><?= $l->trade_control ?></a>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
        <td valign=top width="60%">
            <?= $l->tradingport ?>:&nbsp;
            <?php if ($self->sectorinfo['port_type'] != "none") : ?>
                <a href=port.php><?= ucfirst(t_port($self->sectorinfo['port_type'])); ?></a>
            <?php else : ?>
                <?= $l->none; ?>
            <?php endif; ?>
            <br/>
            <?= $l->planet_in_sec; ?>:
            <table class="table">
                <?php if (empty($self->planets)) : ?>
                    <tr>
                        <td align=center valign=top>
                            <?= $l->none; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach (array_chunk($self->planets, $picsperrow) as $self->planetsOnRow) : ?>
                    <tr>
                        <?php foreach ($self->planetsOnRow as $planet) : ?>
                            <td align=center valign=top>
                                <A HREF="planet.php?planet_id=<?= $planet['planet_id']; ?>">
                                    <img src="images/<?= $planettypes[planetLevel($planet['owner_score'])]; ?>" border=0>
                                </a>
                                <br>
                                <?= empty($planet['name']) ? $l->unnamed : htmlspecialchars($planet['name']); ?>
                                <br> 
                                (<?= empty($planet['owner']) ? $l->unowned : htmlspecialchars($planet['owner_ship_name']); ?>)
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?= $l->ships_in_sec ?>:
            <table class="table">
                <?php if (empty($self->playerinfo['sector'])) : ?>
                    <tr>
                        <td align=center valign=top>
                            <?= $l->sector_0; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if (!empty($self->playerinfo['sector']) && empty($self->shipsInSector)) : ?>
                    <tr>
                        <td align=center valign=top>
                            <?= $l->none; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach (array_chunk($self->shipsInSector, $picsperrow) as $shipsOnRow) : ?>
                    <tr>
                        <?php foreach ($shipsOnRow as $shipInSector) : ?>
                            <td align=center valign=top>
                                <a href="<?= route('ship', ['ship_id' => $shipInSector['ship_id']]); ?>"><img src="images/<?= $shiptypes[shipLevel($shipInSector['score'])]; ?>"></a>
                                <?= htmlspecialchars($shipInSector['ship_name']); ?>
                                    <?php if (!empty($shipInSector['team_name'])) : ?>(<?= htmlspecialchars($shipInSector['team_name']); ?>)<?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?= $l->sector_def; ?>:
            <table class="table">
                <?php if (empty($self->defences)) : ?>
                    <tr>
                        <td align=center>
                            <?= $l->none; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach (array_chunk($self->defences, $picsperrow) as $self->defencesInSector) : ?>
                    <tr>
                        <?php foreach ($self->defencesInSector as $defence) : ?>
                            <td align=center valign=top>
                                <?php if ($defence['defence_type'] == 'F') : ?>
                                    <a href="modify_defences.php?defence_id=<?= $defence['defence_id']; ?>">
                                        <img src="images/fighters.gif" border=0>
                                    </a>
                                    <?php $def_type = $l->fighters; ?>
                                    <?php $def_type .= ($defence['fm_setting'] == 'attack') ? $l->md_attack : $l->md_toll; ?>
                                <?php endif; ?>
                                <?php if ($defence['defence_type'] == 'M') : ?>
                                    <a href="modify_defences.php?defence_id=<?= $defence['defence_id']; ?>">
                                        <img src="images/mines.gif" border=0>
                                    </a>
                                    <?php $def_type = $l->mines; ?>
                                <?php endif; ?>
                                <BR>
                                <?= $defence['ship_name']; ?><br/>
                                <?= $defence['quantity']; ?> <?= $def_type; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </td>
        <td valign=top align="right" width="20%">
            <table> 
                <tr>
                    <td><?= $l->cargo ?></td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <img alt="<?= $l->ore ?>" src="images/ore.gif">&nbsp;<?= $l->ore ?>:
                                <span class="float-end"><?= NUMBER($self->playerinfo['ship_ore']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?= $l->organics ?>" src="images/organics.gif">&nbsp;<?= $l->organics ?>:
                                <span class="float-end"><?= NUMBER($self->playerinfo['ship_organics']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?= $l->goods ?>" src="images/goods.gif">&nbsp;<?= $l->goods ?>:
                                <span class="float-end"><?= NUMBER($self->playerinfo['ship_goods']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?= $l->energy ?>" src="images/energy.gif">&nbsp;<?= $l->energy ?>:
                                <span class="float-end"><?= NUMBER($self->playerinfo['ship_energy']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?= $l->colonists ?>" src="images/colonists.gif">&nbsp;<?= $l->colonists ?>:
                                <span class="float-end"><?= NUMBER($self->playerinfo['ship_colonists']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?= $l->credits ?>" src="images/credits.gif">&nbsp;<?= $l->credits ?>:
                                <span class="float-end"><?= NUMBER($self->playerinfo['credits']); ?></span>
                            </li>
                        </ul>

                    </td>
                </tr>
                <tr>
                    <td><?= $l->realspace ?></td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <a class="nav-link d-inline" href="rsmove.php?engage=1&amp;destination=<?= $self->playerinfo['preset1']; ?>">
                                    =&gt;&nbsp;<?= $self->playerinfo['preset1']; ?>
                                </a>
                                <a class="nav-link d-inline ms-2" href="preset.php">[<?= $l->set; ?>]</a>
                            </li>
                            <li class="list-group-item">
                                <a class="nav-link d-inline" href="rsmove.php?engage=1&amp;destination=<?= $self->playerinfo['preset2']; ?>">
                                    =&gt;&nbsp;<?= $self->playerinfo['preset2']; ?>
                                </a>
                                <a class="nav-link d-inline ms-2" href="preset.php">[<?= $l->set; ?>]</a>
                            </li>
                            <li class="list-group-item">
                                <a class="nav-link d-inline" href="rsmove.php?engage=1&amp;destination=<?= $self->playerinfo['preset3']; ?>">
                                    =&gt;&nbsp;<?= $self->playerinfo['preset3']; ?>
                                </a>
                                <a class="nav-link d-inline ms-2" href="preset.php">[<?= $l->set; ?>]</a>
                            </li>
                            <li class="list-group-item">
                                <a class="nav-link" href="rsmove.php">=&gt;&nbsp;<?= $l->main_other; ?></a>
                            </li>
                        </ul>
                    </td>
                </tr> 
                <tr>
                    <td>
                        <?= $l->main_warpto; ?>
                    </td>
                </tr>
                <tr>
                    <TD>
                        <ul class="list-group">
                            <?php if (empty($self->links)) : ?>
                                <li class="list-group-item">
                                    <a class=dis><?= $l->no_warplink; ?></a>
                                </li>
                            <?php else : ?>
                                <?php foreach ($self->links as $link) : ?>
                                    <li class="list-group-item">
                                        <a href="move.php?sector=<?= $link['link_dest']; ?>">=&gt;&nbsp;<?= $link['link_dest']; ?></a>&nbsp;
                                        <a href="lrscan.php?sector=<?= $link['link_dest']; ?>"><?= $l->scan; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li class="list-group-item">
                                <a class=dis><a class=dis href="lrscan.php?sector=*"><?= $l->fullscan; ?></a>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php include("footer.php"); ?>
