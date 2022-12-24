<TABLE>
    <TR>
        <TD><b><?php echo $l_trade_result; ?></TD>
    </TR>
    <TR> 
        <TD><?php echo $l_cost; ?> : <?php echo $trade_credits; ?>  <?php echo $l_credits; ?></font></b></TD>
    </TR>
    <?php if ($offer->hull_upgrade > $playerinfo->hull) : ?>
        <tr><td colspan="2"><?php echo "$l_hull $l_trade_upgraded $offer->hull_upgrade"; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->engine_upgrade > $playerinfo->engines): ?>
        <tr><td colspan="2"><?php echo "$l_engines $l_trade_upgraded $offer->engine_upgrade"; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->power_upgrade > $playerinfo->power): ?>
        <tr><td colspan="2"><?php echo "$l_power $l_trade_upgraded $offer->power_upgrade"; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->computer_upgrade > $playerinfo->computer): ?>
        <tr><td colspan="2"><?php echo "$l_computer $l_trade_upgraded $offer->computer_upgrade"; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->sensors_upgrade > $playerinfo->sensors): ?>
        <tr><td colspan="2"><?php echo "$l_sensors $l_trade_upgraded $offer->sensors_upgrade"; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->beams_upgrade > $playerinfo->beams): ?>
        <tr><td colspan="2"><?php echo "$l_beams $l_trade_upgraded $offer->beams_upgrade"; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->armor_upgrade > $playerinfo->armor): ?>
        <tr><td colspan="2"><?php echo "$l_armor $l_trade_upgraded $offer->armor_upgrade"; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->cloak_upgrade > $playerinfo->cloak): ?>
        <tr><td colspan="2"><?php echo "$l_cloak $l_trade_upgraded $offer->cloak_upgrade"; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->torp_launchers_upgrade > $playerinfo->torp_launchers): ?>
        <tr><td colspan="2"><?php echo "$l_torp_launch $l_trade_upgraded $offer->torp_launchers_upgrade"; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->shields_upgrade > $playerinfo->shields): ?>
        <tr><td colspan="2"><?php echo "$l_shields $l_trade_upgraded $offer->shields_upgrade"; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->fighter_number): ?>
        <tr><td><?php echo $l_fighters; ?> <?php echo $l_trade_added; ?></td><td><?php echo $offer->fighter_number; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->torpedo_number): ?>
        <tr><td><?php echo $l_torps; ?> <?php echo $l_trade_added; ?></td><td><?php echo $offer->torpedo_number; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->armor_number): ?>
        <tr><td><?php echo $l_armorpts; ?> <?php echo $l_trade_added; ?></td><td><?php echo $offer->armor_number; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->colonist_number): ?>
        <tr><td><?php echo $l_colonists; ?> <?php echo $l_trade_added; ?></td><td><?php echo $offer->colonist_number; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->dev_genesis_number): ?>
        <tr><td><?php echo $l_genesis; ?> <?php echo $l_trade_added; ?></td><td><?php echo $offer->dev_genesis_number; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->dev_beacon_number): ?>
        <tr><td><?php echo $l_beacons; ?> <?php echo $l_trade_added; ?></td><td><?php echo $offer->dev_beacon_number; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->dev_emerwarp_number): ?>
        <tr><td><?php echo $l_ewd; ?> <?php echo $l_trade_added; ?></td><td><?php echo $offer->dev_emerwarp_number; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->dev_warpedit_number): ?>
        <tr><td><?php echo $l_warpedit; ?> <?php echo $l_trade_added; ?></td><td><?php echo $offer->dev_warpedit_number; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->dev_minedeflector_number): ?>
        <tr><td><?php echo $l_deflect; ?> <?php echo $l_trade_added; ?></td><td><?php echo $offer->dev_minedeflector_number; ?></td></tr>
    <?php endif; ?>

    <?php if ($offer->escapepod_purchase && !$playerinfo->dev_escapepod): ?>
        <tr><td><?php echo "$l_escape_pod $l_trade_installed"; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->fuelscoop_purchase && !$playerinfo->dev_fuelscoop): ?>
        <tr><td><?php echo "$l_fuel_scoop $l_trade_installed"; ?></td></tr>
    <?php endif; ?>
    <?php if ($offer->lssd_purchase && !$playerinfo->dev_lssd): ?>
        <tr><td><?php echo "$l_lssd $l_trade_installed"; ?></td></tr>
    <?php endif; ?>
</table>