<?php
global $ksm_allowed;
global $allow_navcomp;
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
            &nbsp;<?= $l->l_turns_have; ?><?= number($self->playerinfo['turns']) ?>
        </td>
        <td align=center>
            <?= $l->l_turns_used ?><?= number($self->playerinfo['turns_used']); ?>
        </td>
        <td align=right>
            <?= $l->l_score ?><?= number($self->playerinfo['score']) ?>&nbsp;
        </td>
    <tr>
        <td>
            &nbsp;<?= $l->l_sector ?>: <?= $self->playerinfo['sector']; ?>
        </td>
        <td align=center>
            <?php if (!empty($self->sectorinfo['beacon'])) : ?>
                <?= $self->sectorinfo['beacon']; ?>
            <?php endif; ?>

            <?php if ($self->zoneinfo['zone_id'] < 5) : ?>
                <?php $self->zoneinfo['zone_name'] = $l->l_zname[$self->zoneinfo['zone_id']]; ?>
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
                    <td><?= $l->l_commands ?></td>
                </tr>
                <TR>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item"><a class="nav-link" href="<?= route('device'); ?>"><?= $l->l_devices ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('planet_report.php'); ?>"><?= $l->l_planets ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('log'); ?>"><?= $l->l_log ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('defence_report'); ?>"><?= $l->l_sector_def ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('messages', ['read' => 1, 'send' => 1]); ?>"><?= $l->l_main_messages ?></A></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('teams.php'); ?>"><?= $l->l_teams ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('self_destruct'); ?>"><?= $l->l_ohno ?></a></li>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('options'); ?>"><?= $l->l_options ?></a></li>
                            <?php if (!empty($ksm_allowed)) : ?>
                                <li class="list-group-item"><a class="nav-link" href="<?= route('galaxy'); ?>"><?= $l->l_map ?></a></li>
                            <?php endif; ?>
                            <?php if (!empty($allow_navcomp)) : ?>
                                <li class="list-group-item"><a class="nav-link" href="<?= route('navcomp'); ?>"><?= $l->l_navcomp ?></a></li>
                            <?php endif; ?>
                            <li class="list-group-item"><a class="nav-link" href="<?= route('logout'); ?>"><?= $l->l_logout ?></a></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td><?= $l->l_traderoutes ?></td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group">
                            <?php if (empty($self->traderoutes)) : ?>
                                <li class="list-group-item">
                                    <?= $l->l_none; ?>
                                </li>
                            <?php endif; ?>
                            <?php foreach ($self->traderoutes as $traderoute) : ?>
                                <li class="list-group-item">
                                    <a href="traderoute.php?engage=<?= $traderoute['traderoute_id']; ?>">
                                        <?php if ($traderoute['source_type'] == 'P') : ?>
                                            <?= $l->l_port; ?>
                                        <?php elseif ($traderoute['source_type'] == 'D') : ?>
                                            <?= "Def's "; ?>
                                        <?php else : ?>
                                            <?= empty($traderoute['planet_source']) ? $l->l_unnamed : $traderoute['planet_source']; ?>
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
                                            <?= empty($traderoute['planet_dest']) ? $l->l_unnamed : $traderoute['planet_dest']; ?>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>

                            <li class="list-group-item">
                                <a href=traderoute.php><?= $l->l_trade_control ?></a>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
        <td valign=top width="60%">
            <?= $l->l_tradingport ?>:&nbsp;
            <?php if ($self->sectorinfo['port_type'] != "none") : ?>
                <a href=port.php><?= ucfirst(t_port($self->sectorinfo['port_type'])); ?></a>
            <?php else : ?>
                <?= $l->l_none; ?>
            <?php endif; ?>
            <br/>
            <?= $l->l_planet_in_sec; ?>:
            <table class="table">
                <?php if (empty($self->planets)) : ?>
                    <tr>
                        <td align=center valign=top>
                            <?= $l->l_none; ?>
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
                                <?= empty($planet['name']) ? $l->l_unnamed : htmlspecialchars($planet['name']); ?>
                                <br> 
                                (<?= empty($planet['owner']) ? $l->l_unowned : htmlspecialchars($planet['owner_ship_name']); ?>)
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?= $l->l_ships_in_sec ?>:
            <table class="table">
                <?php if (empty($self->playerinfo['sector'])) : ?>
                    <tr>
                        <td align=center valign=top>
                            <?= $l->l_sector_0; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if (!empty($self->playerinfo['sector']) && empty($self->shipsInSector)) : ?>
                    <tr>
                        <td align=center valign=top>
                            <?= $l->l_none; ?>
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
            <?= $l->l_sector_def; ?>:
            <table class="table">
                <?php if (empty($self->defences)) : ?>
                    <tr>
                        <td align=center>
                            <?= $l->l_none; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach (array_chunk($self->defences, $picsperrow) as $self->defencesInSector) : ?>
                    <tr>
                        <?php foreach ($self->defencesInSector as $defence) : ?>
                            <td align=center valign=top>
                                <?php if ($defence['defence_type'] == 'F') : ?>
                                    <a href="<?= route('modify_defences', ['defence_id' => $defence['defence_id']]); ?>">
                                        <img src="images/fighters.gif" border=0>
                                    </a>
                                    <?php $def_type = $l->l_fighters; ?>
                                    <?php $def_type .= ($defence['fm_setting'] == 'attack') ? $l->l_md_attack : $l->l_md_toll; ?>
                                <?php endif; ?>
                                <?php if ($defence['defence_type'] == 'M') : ?>
                                    <a href="<?= route('modify_defences', ['defence_id' => $defence['defence_id']]); ?>">
                                        <img src="images/mines.gif" border=0>
                                    </a>
                                    <?php $def_type = $l->l_mines; ?>
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
                    <td><?= $l->l_cargo ?></td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <img alt="<?= $l->l_ore ?>" src="images/ore.gif">&nbsp;<?= $l->l_ore ?>:
                                <span class="float-end"><?= number($self->playerinfo['ship_ore']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?= $l->l_organics ?>" src="images/organics.gif">&nbsp;<?= $l->l_organics ?>:
                                <span class="float-end"><?= number($self->playerinfo['ship_organics']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?= $l->l_goods ?>" src="images/goods.gif">&nbsp;<?= $l->l_goods ?>:
                                <span class="float-end"><?= number($self->playerinfo['ship_goods']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?= $l->l_energy ?>" src="images/energy.gif">&nbsp;<?= $l->l_energy ?>:
                                <span class="float-end"><?= number($self->playerinfo['ship_energy']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?= $l->l_colonists ?>" src="images/colonists.gif">&nbsp;<?= $l->l_colonists ?>:
                                <span class="float-end"><?= number($self->playerinfo['ship_colonists']); ?></span>
                            </li>
                            <li class="list-group-item">
                                <img alt="<?= $l->l_credits ?>" src="images/credits.gif">&nbsp;<?= $l->l_credits ?>:
                                <span class="float-end"><?= number($self->playerinfo['credits']); ?></span>
                            </li>
                        </ul>

                    </td>
                </tr>
                <tr>
                    <td><?= $l->l_realspace ?></td>
                </tr>
                <tr>
                    <td>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <a class="nav-link d-inline" href="<?= route('rsmove', ['engage' => 1, 'sector' => $self->playerinfo['preset1']]); ?>">
                                    =&gt;&nbsp;<?= $self->playerinfo['preset1']; ?>
                                </a>
                                <a class="nav-link d-inline ms-2" href="<?= route('preset'); ?>">[<?= $l->l_set; ?>]</a>
                            </li>
                            <li class="list-group-item">
                                <a class="nav-link d-inline" href="<?= route('rsmove', ['engage' => 1, 'sector' => $self->playerinfo['preset2']]); ?>">
                                    =&gt;&nbsp;<?= $self->playerinfo['preset2']; ?>
                                </a>
                                <a class="nav-link d-inline ms-2" href="<?= route('preset'); ?>">[<?= $l->l_set; ?>]</a>
                            </li>
                            <li class="list-group-item">
                                <a class="nav-link d-inline" href="<?= route('rsmove', ['engage' => 1, 'sector' => $self->playerinfo['preset3']]); ?>">
                                    =&gt;&nbsp;<?= $self->playerinfo['preset3']; ?>
                                </a>
                                <a class="nav-link d-inline ms-2" href="<?= route('preset'); ?>">[<?= $l->l_set; ?>]</a>
                            </li>
                            <li class="list-group-item">
                                <a class="nav-link" href="<?= route('rsmove');?>">=&gt;&nbsp;<?= $l->l_main_other; ?></a>
                            </li>
                        </ul>
                    </td>
                </tr> 
                <tr>
                    <td>
                        <?= $l->l_main_warpto; ?>
                    </td>
                </tr>
                <tr>
                    <TD>
                        <ul class="list-group">
                            <?php if (empty($self->links)) : ?>
                                <li class="list-group-item">
                                    <a class=dis><?= $l->l_no_warplink; ?></a>
                                </li>
                            <?php else : ?>
                                <?php foreach ($self->links as $link) : ?>
                                    <li class="list-group-item">
                                        <form action="<?= route('move', ['sector' => $link['link_dest']]); ?>" method="post" class="d-inline">
                                            <a href="javascript:;" onclick="parentNode.submit();">=&gt;&nbsp;<?= $link['link_dest']; ?></a>&nbsp;
                                        </form>
                                        <a href="<?= route('lrscan_sector', ['sector' => $link['link_dest']]); ?>"><?= $l->l_scan; ?></a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li class="list-group-item">
                                <a class=dis><a class=dis href="<?= route('lrscan');?>"><?= $l->l_fullscan; ?></a>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php include("footer.php"); ?>
