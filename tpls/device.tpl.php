<?php $title = $l_device_title; ?>
<?php include 'header.php'; ?>
<?php bigtitle(); ?>
<?php echo $l_device_expl; ?>
<BR><BR>
<TABLE class="table table-hover">
    <TR>
        <TD><B><?php echo $l_device; ?></B></TD>
        <TD><B><?php echo $l_qty; ?></B></TD>
        <TD><B><?php echo $l_usage; ?></B></TD>
    </TR>
    <TR>
        <TD><A HREF=beacon.php><?php echo $l_beacons; ?></A></TD>
        <TD><?php echo NUMBER($playerinfo['dev_beacon']); ?></TD>
        <TD><?php echo $l_manual; ?></TD>
    </TR>
    <TR >
        <TD><A HREF=warpedit.php><?php echo $l_warpedit; ?></A></TD>
        <TD><?php echo NUMBER($playerinfo['dev_warpedit']); ?></TD>
        <TD><?php echo $l_manual; ?></TD>
    </TR>
    <TR>
        <TD><A HREF=genesis.php><?php echo $l_genesis; ?></A></TD>
        <TD><?php echo NUMBER($playerinfo['dev_genesis']); ?></TD>
        <TD><?php echo $l_manual; ?></TD>
    </TR>
    <TR >
        <TD><?php echo $l_deflect; ?></TD>
        <TD><?php echo NUMBER($playerinfo['dev_minedeflector']); ?></TD>
        <TD><?php echo $l_automatic; ?></TD>
    </TR>
    <TR>
        <TD><A HREF=mines.php?op=1><?php echo $l_mines; ?></A></TD>
        <TD><?php echo NUMBER($playerinfo['torps']); ?></TD>
        <TD><?php echo $l_manual; ?></TD>
    </TR>
    <TR >
        <TD><A HREF=mines.php?op=2><?php echo $l_fighters; ?></A></TD>
        <TD><?php echo NUMBER($playerinfo['ship_fighters']); ?></TD>
        <TD><?php echo $l_manual; ?></TD>
    </TR>
    <TR>
        <TD><A HREF=emerwarp.php><?php echo $l_ewd; ?></A></TD>
        <TD><?php echo NUMBER($playerinfo['dev_emerwarp']); ?></TD>
        <TD><?php echo $l_manual; ?>/<?php echo $l_automatic; ?></TD>
    </TR>
    <TR >
        <TD><?php echo $l_escape_pod; ?></TD>
        <TD>
            <?php echo ($playerinfo['dev_escapepod'] == 'Y') ? $l_yes : $l_no; ?>
        </TD>
        <TD><?php echo $l_automatic; ?></TD>
    </TR>
    <TR>
        <TD><?php echo $l_fuel_scoop; ?></TD>
        <TD>
            <?php echo ($playerinfo['dev_fuelscoop'] == 'Y') ? $l_yes : $l_no; ?>
        </TD>
        <TD><?php echo $l_automatic; ?></TD>
    </TR>
    <TR >
        <TD><?php echo $l_lssd; ?></TD>
        <TD>
            <?php echo ($playerinfo['dev_lssd'] == 'Y') ? $l_yes : $l_no; ?>
        </TD>
        <TD><?php echo $l_automatic; ?></TD>
    </TR>
</TABLE>
<BR>
<?php include 'footer.php'; ?>
