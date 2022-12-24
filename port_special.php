<?php if ($totalBounty > 0): ?>
    <?php echo $l_port_bounty; ?>
    <?php echo str_replace("[amount]", NUMBER($totalBounty), $l_port_bounty2); ?>
    <A HREF="bounty.php"><?php echo $l_by_placebounty; ?></A><BR><BR>
    <?php TEXT_GOTOMAIN(); ?>
<?php else: ?>
        <?php echo $l_would_dump; ?> <A HREF=dump.php><?php echo $l_here; ?></A>.
    <A HREF="bounty.php"><?php echo $l_by_placebounty; ?></A><BR>
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
                <td><?php echo $l_genesis;?></td>
                <td><?php echo NUMBER($dev_genesis_price); ?></td>
                <td><?php echo NUMBER($playerinfo->dev_genesis); ?></td>
                <td><?php echo $l_unlimited; ?></td>
                <td><INPUT TYPE=TEXT NAME=dev_genesis_number SIZE=4 MAXLENGTH=4 VALUE=0 ></td>
                <td><?php echo $l_hull;?></td>
                <td><input type=text readonly name=hull_costper VALUE='0' ></td>
                <td><?php echo NUMBER($playerinfo->hull); ?></td>
                <td>
                    <?php echo dropdown("hull_upgrade", $playerinfo->hull); ?>
                </td>
            </TR>
            <TR>
                <td><?php echo $l_beacons;?></td>
                <td><?php echo NUMBER($dev_beacon_price); ?></td>
                <td><?php echo NUMBER($playerinfo->dev_beacon); ?></td>
                <td><?php echo $l_unlimited; ?></td>
                <td><INPUT TYPE=TEXT NAME=dev_beacon_number SIZE=4 MAXLENGTH=4 VALUE=0 ></td>
                <td><?php echo $l_engines;?></td>
                <td><input type=text readonly  size=10 name=engine_costper VALUE='0'  ></td>
                <td><?php echo NUMBER($playerinfo->engines); ?></td>
                <td>
                    <?php echo dropdown("engine_upgrade", $playerinfo->engines); ?>
                </td>
            </TR>
            <TR >
                <td><?php echo $l_ewd;?></td>
                <td><?php echo NUMBER($dev_emerwarp_price); ?></td>
                <td><?php echo NUMBER($playerinfo->dev_emerwarp); ?></td>

                <?php if ($playerinfo->dev_emerwarp != $max_emerwarp) : ?>
                    <td>
                        <?php echo NUMBER($portSpecial->emerwarp_free); ?>
                    </td>
                    <td><INPUT TYPE=TEXT NAME=dev_emerwarp_number SIZE=4 MAXLENGTH=4 VALUE=0 >
                    <?php else : ?>
                    <td>0</td>
                    <td><input type=text readonly NAME=dev_emerwarp_number MAXLENGTH=10 value="<?php echo $l_full; ?>"  >
                    <?php endif; ?>
                </td>
                <td><?php echo $l_power;?></td>
                <td><input type=text readonly name=power_costper VALUE='0'  ></td>
                <td><?php echo NUMBER($playerinfo->power); ?></td>
                <td>
                    <?php echo dropdown("power_upgrade", $playerinfo->power); ?>
                </td>
            </TR>
            <TR>
                <td><?php echo $l_warpedit;?></td>
                <td><?php echo NUMBER($dev_warpedit_price); ?></td>
                <td><?php echo NUMBER($playerinfo->dev_warpedit); ?></td><td><?php echo $l_unlimited; ?></td><td><INPUT TYPE=TEXT NAME=dev_warpedit_number SIZE=4 MAXLENGTH=4 VALUE=0 ></td>
                <td><?php echo $l_computer;?></td>
                <td><input type=text readonly  name=computer_costper VALUE='0'  ></td>
                <td><?php echo NUMBER($playerinfo->computer); ?></td>
                <td>
                    <?php echo dropdown("computer_upgrade", $playerinfo->computer); ?>
                </td>
            </TR>
            <TR>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><?php echo $l_sensors;?></td>
                <td><input type=text readonly name=sensors_costper VALUE='0'  ></td>
                <td><?php echo NUMBER($playerinfo->sensors); ?></td>
                <td>
                    <?php echo dropdown("sensors_upgrade", $playerinfo->sensors); ?>
                </td>
            </TR>
            <TR >
                <td><?php echo $l_deflect;?></td>
                <td><?php echo NUMBER($dev_minedeflector_price); ?></td>
                <td><?php echo NUMBER($playerinfo->dev_minedeflector); ?></td>
                <td><?php echo $l_unlimited; ?></td>
                <td><INPUT TYPE=TEXT NAME=dev_minedeflector_number SIZE=4 MAXLENGTH=10 VALUE=0 ></td>
                <td><?php echo $l_beams;?></td>
                <td><input type=text readonly  name=beams_costper VALUE='0'  ></td>
                <td><?php echo NUMBER($playerinfo->beams); ?></td>
                <td>
                    <?php echo dropdown("beams_upgrade", $playerinfo->beams); ?>
                </td>
            </TR>
            <TR>
                <td><?php echo $l_escape_pod;?></td>
                <td><?php echo NUMBER($dev_escapepod_price); ?></td>
                <?php if ($playerinfo->dev_escapepod == "N") : ?>
                    <td><?php echo $l_none; ?></td>
                    <td>&nbsp;</td>
                    <td><INPUT TYPE=CHECKBOX NAME=escapepod_purchase VALUE=1 $onclick></td>
                <?php else : ?>
                    <td><?php echo $l_equipped; ?></td>
                    <td></td>
                    <td><?php echo $l_n_a; ?></td>
                <?php endif; ?>
                <td><?php echo $l_armor;?></td>
                <td><input type=text readonly name=armor_costper VALUE='0'  ></td>
                <td><?php echo NUMBER($playerinfo->armor); ?></td>
                <td>
                    <?php echo dropdown("armor_upgrade", $playerinfo->armor); ?>
                </td>
            </TR>
            <TR>
                <td><?php echo $l_fuel_scoop;?></td>
                <td><?php echo NUMBER($dev_fuelscoop_price); ?></td>
                <?php if ($playerinfo->dev_fuelscoop == "N") : ?>
                    <td><?php echo $l_none; ?></td>
                    <td>&nbsp;</td>
                    <td><INPUT TYPE=CHECKBOX NAME=fuelscoop_purchase VALUE=1 $onclick></td>
                <?php else : ?>
                    <td><?php echo $l_equipped; ?></td>
                    <td></td>
                    <td><?php echo $l_n_a; ?></td>
                <?php endif; ?>
                <td><?php echo $l_cloak;?></td>
                <td><input type=text readonly  name=cloak_costper VALUE='0'   $onfocus></td>
                <td><?php echo NUMBER($playerinfo->cloak); ?></td>
                <td>
                    <?php echo dropdown("cloak_upgrade", $playerinfo->cloak); ?>
                </td>
            </TR>
            <TR>
                <td><?php echo $l_lssd;?></td>
                <td><?php echo NUMBER($dev_lssd_price); ?></td>
                <?php if ($playerinfo->dev_lssd == "N") : ?>
                    <td><?php echo $l_none; ?></td>
                    <td>&nbsp;</td>
                    <td><INPUT TYPE=CHECKBOX NAME=lssd_purchase VALUE=1 $onclick></td>
                <?php else : ?>
                    <td><?php echo $l_equipped; ?></td>
                    <td></td>
                    <td><?php echo $l_n_a; ?></td>
                <?php endif; ?>
                <td><?php echo $l_torp_launch;?></td>
                <td><input type=text readonly name=torp_launchers_costper VALUE='0'  ></td>
                <td><?php echo NUMBER($playerinfo->torp_launchers); ?></td>
                <td>
                    <?php echo dropdown("torp_launchers_upgrade", $playerinfo->torp_launchers); ?>
                </td>
            </TR>
            <TR >
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><?php echo $l_shields;?></td>
                <td><input type=text readonly  name=shields_costper VALUE='0'  ></td>
                <td><?php echo NUMBER($playerinfo->shields); ?></td>
                <td>
                    <?php echo dropdown("shields_upgrade", $playerinfo->shields); ?>
                </td>
            </TR>
        </TABLE>
        <BR>
        <TABLE>
            <TR>
                <td><?php echo $l_item; ?></td>
                <td><?php echo $l_cost; ?></td>
                <td><?php echo $l_current; ?></td>
                <td><?php echo $l_max; ?></td>
                <td><?php echo $l_qty; ?></td>
            </TR>
            <TR >
                <td><?php echo $l_fighters; ?></td>
                <td><?php echo NUMBER($fighter_price); ?></td>
                <td><?php echo NUMBER($playerinfo->ship_fighters); ?> / <?php echo NUMBER($portSpecial->fighter_max); ?></td>
                <?php if ($playerinfo->ship_fighters != $portSpecial->fighter_max) : ?>
                    <td><?php echo NUMBER($portSpecial->fighter_free); ?></td>
                    <td><INPUT TYPE=TEXT NAME=fighter_number SIZE=6 MAXLENGTH=10 VALUE=0 ></td>
                <?php else : ?>
                    <td>0</td>
                    <td><input type=text readonly NAME=fighter_number MAXLENGTH=10 value="<?php echo $l_full; ?>" abindex='-1'>                </td>
                <?php endif; ?>
            </tr>
            <tr>
                <td><?php echo $l_torps; ?></td>
                <td><?php echo NUMBER($torpedo_price); ?></td>
                <td><?php echo NUMBER($playerinfo->torps); ?> / <?php echo NUMBER($portSpecial->torpedo_max); ?></td>
                <?php if ($playerinfo->torps != $portSpecial->torpedo_max) : ?>
                    <td><?php echo NUMBER($portSpecial->torpedo_free); ?></td>
                    <td><INPUT TYPE=TEXT NAME=torpedo_number SIZE=6 MAXLENGTH=10 VALUE=0 ></td>
                <?php else : ?>
                    <td>0</td>
                    <td><input type=text readonly NAME=torpedo_number MAXLENGTH=10 value="<?php echo $l_full; ?>"  ></td>
                <?php endif; ?>
            </TR>
            <TR >
                <td><?php echo $l_armorpts; ?></td>
                <td><?php echo NUMBER($armor_price); ?></td>
                <td><?php echo NUMBER($playerinfo->armor_pts); ?> / <?php echo NUMBER($portSpecial->armor_max); ?></td>
                <?php if ($playerinfo->armor_pts != $portSpecial->armor_max) : ?>
                    <td> <?php echo NUMBER($portSpecial->armor_free); ?></td>
                    <td> <INPUT TYPE=TEXT NAME=armor_number SIZE=6 MAXLENGTH=10 VALUE=0></td>
                <?php else : ?>
                    <td>0</td>
                    <td><input type=text readonly  NAME=armor_number MAXLENGTH=10 value="<?php echo $l_full; ?>" ></td>
                <?php endif; ?>
            </tr>
            <tr>
                <td><?php echo $l_colonists; ?></td>
                <td><?php echo NUMBER($colonist_price); ?></td>
                <td><?php echo NUMBER($playerinfo->ship_colonists); ?> / <?php echo NUMBER($portSpecial->colonist_max); ?></td>
                <?php if ($playerinfo->ship_colonists != $portSpecial->colonist_max) : ?>
                    <td><?php echo NUMBER($portSpecial->colonist_free); ?></td>
                    <td><INPUT TYPE=TEXT NAME=colonist_number SIZE=6 MAXLENGTH=10 VALUE=0 ></td>
                <?php else : ?>
                    <td>0</td>
                    <td><input type=text readonly  NAME=colonist_number MAXLENGTH=10 value="<?php echo $l_full; ?>" ></td>
                <?php endif; ?>
            </TR>
        </TABLE>
        <TABLE>
            <TR>
                <td><INPUT TYPE=SUBMIT VALUE="<?php echo $l_buy; ?>"></td>
                <TD ALIGN=RIGHT><?php echo $l_totalcost; ?>: <INPUT TYPE=TEXT  NAME=total_cost SIZE=22 VALUE=0></td>
            </TR>
        </TABLE>
    </FORM>
<?php endif; ?>