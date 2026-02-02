<?php $title = $l_report_title; ?>
<?php include 'header.php'; ?>
<?php bigtitle(); ?>
<TABLE class="table">
    <tr>
        <td><?php echo $l_player; ?>: <?php echo htmlspecialchars($playerinfo['character_name']); ?></td>
        <TD ALIGN=CENTER><?php echo $l_ship; ?>: <?php echo htmlspecialchars($playerinfo['ship_name']); ?></td>
        <TD ALIGN=RIGHT><?php echo $l_credits; ?>: <?php echo NUMBER($playerinfo['credits']); ?></td>
    </tr>
    <TR>
        <td>
            <TABLE class="table table-hover">
                <tr><td><?php echo $l_ship_levels; ?></td><td></td></tr>
                <tr><td><?php echo $l_hull; ?></td><td><?php echo $l_level; ?> <?php echo $playerinfo['hull']; ?></td></tr>
                <tr><td><?php echo $l_engines; ?></td><td><?php echo $l_level; ?> <?php echo $playerinfo['engines']; ?></td></tr>
                <tr><td><?php echo $l_power; ?></td><td><?php echo $l_level; ?> <?php echo $playerinfo['power']; ?></td></tr>
                <tr><td><?php echo $l_computer; ?></td><td><?php echo $l_level; ?> <?php echo $playerinfo['computer']; ?></td></tr>
                <tr><td><?php echo $l_sensors; ?></td><td><?php echo $l_level; ?> <?php echo $playerinfo['sensors']; ?></td></tr>
                <tr><td><?php echo $l_armor; ?></td><td><?php echo $l_level; ?> <?php echo $playerinfo['armor']; ?></td></tr>
                <tr><td><?php echo $l_shields; ?></td><td><?php echo $l_level; ?> <?php echo $playerinfo['shields']; ?></td></tr>
                <tr><td><?php echo $l_beams; ?></td><td><?php echo $l_level; ?> <?php echo $playerinfo['beams']; ?></td></tr>
                <tr><td><?php echo $l_torp_launch; ?></td><td><?php echo $l_level; ?> <?php echo $playerinfo['torp_launchers']; ?></td></tr>
                <tr><td><?php echo $l_cloak; ?></td><td><?php echo $l_level; ?> <?php echo $playerinfo['cloak']; ?></td></tr>
                <tr><td><i><?php echo $l_shipavg; ?></i></td><td><?php echo $l_level; ?> <?php echo NUMBER($shipavg, 2); ?></td></tr>
            </TABLE>
        </td>
        <td VALIGN=TOP>
            <TABLE  class="table table-hover">
                <tr>
                    <td><?php echo $l_holds; ?></td>
                    <TD ALIGN=RIGHT>
                        <?php echo NUMBER($playerinfo['ship_ore'] + $playerinfo['ship_organics'] + $playerinfo['ship_goods'] + $playerinfo['ship_colonists']); ?> / <?php echo NUMBER(NUM_HOLDS($playerinfo['hull'])); ?>
                    </td>
                </tr>
                <tr><td><?php echo $l_ore; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($playerinfo['ship_ore']); ?></td></tr>
                <tr><td><?php echo $l_organics; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($playerinfo['ship_organics']); ?></td></tr>
                <tr><td><?php echo $l_goods; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($playerinfo['ship_goods']); ?></td></tr>
                <tr><td><?php echo $l_colonists; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($playerinfo['ship_colonists']); ?></td></tr>
                <TR><td>&nbsp;</td></tr>
                <tr><td><?php echo $l_arm_weap; ?></td><td></td></tr>
                <tr><td><?php echo $l_armorpts; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($playerinfo['armor_pts']); ?> / <?php echo NUMBER(NUM_ARMOUR($playerinfo['armor'])); ?></td></tr>
                <tr><td><?php echo $l_fighters; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($playerinfo['ship_fighters']); ?> / <?php echo NUMBER(NUM_FIGHTERS($playerinfo['computer'])); ?></td></tr>
                <tr><td><?php echo $l_torps; ?></td><TD ALIGN=RIGHT><?php echo NUMBER($playerinfo['torps']); ?> / <?php echo NUMBER(NUM_TORPEDOES($playerinfo['torp_launchers'])); ?></td></tr>
            </TABLE>
        </td>
        <td VALIGN=TOP>
            <TABLE  class="table table-hover">
                <tr>
                    <td><?php echo $l_energy; ?></td>
                    <TD ALIGN=RIGHT><?php echo NUMBER($playerinfo['ship_energy']); ?> / <?php echo NUMBER(NUM_ENERGY($playerinfo['power'])); ?></td>
                </tr>
                <TR><td>&nbsp;</td></tr>
                <tr><td><?php echo $l_devices; ?></td><td></td></tr>
                <tr><td><?php echo $l_beacons; ?></td><TD ALIGN=RIGHT><?php echo $playerinfo['dev_beacon']; ?></td></tr>
                <tr><td><?php echo $l_warpedit; ?></td><TD ALIGN=RIGHT><?php echo $playerinfo['dev_warpedit']; ?></td></tr>
                <tr><td><?php echo $l_genesis; ?></td><TD ALIGN=RIGHT><?php echo $playerinfo['dev_genesis']; ?></td></tr>
                <tr><td><?php echo $l_deflect; ?></td><TD ALIGN=RIGHT><?php echo $playerinfo['dev_minedeflector']; ?></td></tr>
                <tr><td><?php echo $l_ewd; ?></td><TD ALIGN=RIGHT><?php echo $playerinfo['dev_emerwarp']; ?></td></tr>
                <tr><td><?php echo $l_escape_pod; ?></td><TD ALIGN=RIGHT><?php echo ($playerinfo['dev_escapepod'] == 'Y') ? $l_yes : $l_no; ?></td></tr>
                <tr><td><?php echo $l_fuel_scoop; ?></td><TD ALIGN=RIGHT><?php echo ($playerinfo['dev_fuelscoop'] == 'Y') ? $l_yes : $l_no; ?></td></tr>
                <tr><td><?php echo $l_lssd; ?></td><TD ALIGN=RIGHT><?php echo ($playerinfo['dev_lssd'] == 'Y') ? $l_yes : $l_no; ?></td></tr>
            </TABLE>
        </td></tr>
</TABLE>

<p align=center>
    <img src="images/<?php echo $shiptypes[shipLevel($playerinfo)]; ?>" border=0>
</p>
<?php include 'footer.php'; ?>

