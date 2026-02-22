<?php

function YesNoLimit($value, $other = null): string
{
    global $l_zi_allow;
    global $l_zi_notallow;
    global $l_zi_limit;

    if ($value == 'Y') {
        return $l_zi_allow;
    } elseif ($value == 'N') {
        return $l_zi_notallow;
    } elseif ($value == 'L') {
        return $l_zi_limit;
    } else {
        return $other;
    }
}
?>
<?php $title = $l_zi_title; ?>
<?php include 'header.php'; ?>
<?php bigtitle(); ?>
<?php if (empty($zoneinfo)) : ?>
    <?php echo $l_zi_nexist; ?>
<?php else : ?>
    <table class="table">
        <?php if ($isAllowChangeZone) : ?>
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
                        <td width="50%"><?php echo $l_zi_owner; ?></td>
                        <td width="50%"><?php echo $ownername; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $l_beacons; ?></td>
                        <td><?php echo YesNoLimit($zoneinfo['allow_beacon']); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $l_attack; ?></td>
                        <td><?php echo YesNoLimit($zoneinfo['allow_attack']); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $l_modify_defence; ?></td>
                        <td><?php echo YesNoLimit($zoneinfo['allow_defenses']); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $l_warpedit; ?></td>
                        <td><?php echo YesNoLimit($zoneinfo['allow_warpedit']); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $l_planets; ?></td>
                        <td><?php echo YesNoLimit($zoneinfo['allow_planet']); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $l_port; ?></td>
                        <td><?php echo YesNoLimit($zoneinfo['allow_trade']); ?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;<?php echo $l_zi_maxhull; ?></td>
                        <td><?php if (empty($zoneinfo['max_hull'])): ?><?php echo $l_zi_ul; ?><?php else: ?><?php echo $zoneinfo['max_hull']; ?><?php endif; ?> </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php endif; ?>
<?php include 'footer.php'; ?>
