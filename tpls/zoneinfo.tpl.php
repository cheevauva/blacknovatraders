<?php $title = $l_zi_title; ?>
<?php include 'header.php'; ?>
<?php bigtitle(); ?>
<?php if (empty($zoneinfo)): ?>
    <?php echo $l_zi_nexist; ?>
<?php else: ?>

    <table class="table">
        <?php if ($isAllowChangeZone): ?>
            <tr>
                <td>
                    <div class="alert alert-info">
                        <?php echo $l_zi_control; ?>. <a href="zoneedit.php?zone=<?php echo $zone; ?>"><?php echo $l_clickme; ?></a> <?php echo $l_zi_tochange; ?>
                    </div>

                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td align="center" colspan="2"><?php echo htmlspecialchars($zoneinfo['zone_name']); ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="table table-hover">
                    <tr>
                        <td width="50%">&nbsp;<?php echo $l_zi_owner; ?></td>
                        <td width="50%"><?php echo $ownername; ?>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;<?php echo $l_beacons; ?></td>
                        <td><?php echo $beacon; ?>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;<?php echo $l_att_att; ?></td>
                        <td><?php echo $attack; ?>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;<?php echo $l_md_title; ?></td>
                        <td><?php echo $defense; ?>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;<?php echo $l_warpedit; ?></td>
                        <td><?php echo $warpedit; ?>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;<?php echo $l_planets; ?></td>
                        <td><?php echo $planet; ?>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;<?php echo $l_title_port; ?></td>
                        <td><?php echo $trade; ?>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;<?php echo $l_zi_maxhull; ?></td>
                        <td><?php echo $hull; ?>&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php endif; ?>
<?php include 'footer.php'; ?>
