<?php

function YesNoLimit($value, $other = null): string
{
    global $l;

    if ($value == 'Y') {
        return $l->l_zi_allow;
    } elseif ($value == 'N') {
        return $l->l_zi_notallow;
    } elseif ($value == 'L') {
        return $l->l_zi_limit;
    } else {
        return $other;
    }
}
?>
<?php $self = BNT\Controller\ZoneinfoController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<?php if (empty($self->zoneinfo)) : ?>
    <?= $l->l_zi_nexist; ?>
<?php else : ?>
    <table class="table">
        <?php if ($self->isAllowChangeZone) : ?>
            <tr>
                <td>
                    <div class="alert alert-info">
                        <?= $l->l_zi_control; ?>. <a href="<?= route('zoneedit', ['zone' => $self->zone]); ?>"><?= $l->l_clickme; ?></a> <?= $l->l_zi_tochange; ?>
                    </div>

                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td align="center" colspan="2"><?= htmlspecialchars($self->zoneinfo['zone_name']); ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="table table-hover">
                    <tr>
                        <td width="50%"><?= $l->l_zi_owner; ?></td>
                        <td width="50%"><?= htmlspecialchars($self->ownername); ?></td>
                    </tr>
                    <tr>
                        <td><?= $l->l_beacons; ?></td>
                        <td><?= YesNoLimit($self->zoneinfo['allow_beacon']); ?></td>
                    </tr>
                    <tr>
                        <td><?= $l->l_attack; ?></td>
                        <td><?= YesNoLimit($self->zoneinfo['allow_attack']); ?></td>
                    </tr>
                    <tr>
                        <td><?= $l->l_modify_defence; ?></td>
                        <td><?= YesNoLimit($self->zoneinfo['allow_defenses']); ?></td>
                    </tr>
                    <tr>
                        <td><?= $l->l_warpedit; ?></td>
                        <td><?= YesNoLimit($self->zoneinfo['allow_warpedit']); ?></td>
                    </tr>
                    <tr>
                        <td><?= $l->l_planets; ?></td>
                        <td><?= YesNoLimit($self->zoneinfo['allow_planet']); ?></td>
                    </tr>
                    <tr>
                        <td><?= $l->l_port; ?></td>
                        <td><?= YesNoLimit($self->zoneinfo['allow_trade']); ?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;<?= $l->l_zi_maxhull; ?></td>
                        <td><?php if (empty($self->zoneinfo['max_hull'])): ?><?= $l->l_zi_ul; ?><?php else: ?><?= $self->zoneinfo['max_hull']; ?><?php endif; ?> </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<?php endif; ?>
<?php include_footer(); ?>
