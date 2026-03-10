<?php $self = BNT\Controller\LongRangeScanSectorController::as($self); ?>
<?= include_header(); ?>
<?= bigtitle(); ?>
<h5><?= $l->l_sector; ?> <?= $self->sector; ?> <?php if (!empty($self->sectorinfo['sector_name'])) : ?>(<?= htmlspecialchars($self->sectorinfo['sector_name']); ?>)<?php endif; ?></h5>
<?= $l->l_lrs_used; ?> 1 <?= $l->l_lrs_turns; ?> <?= number($self->playerinfo['turns']); ?> <?= $l->l_lrs_left; ?><BR><BR>
<table class="table">
    <tr>
        <th><?= $l->l_port; ?></th>
        <th></th>
    </tr>
    <tr>
        <td><?php if ($self->sectorinfo['port_type'] !== 'none'): ?><img src="images/<?= $self->sectorinfo['port_type']; ?>.gif"><?php endif; ?> <?= t_port($self->sectorinfo['port_type']); ?></td>
        <td><?= implode(':', [$self->sectorinfo['distance'], $self->sectorinfo['angle1'], $self->sectorinfo['angle2']]); ?></td>
    </tr>
    <tr>
        <th><?= $l->l_mines; ?></th>
        <th><?= $l->l_fighters; ?></th>
    </tr>
    <tr>
        <td><?= $self->mines; ?></td>
        <td><?= $self->fighters; ?></td>
    </tr>
    <tr>
        <th><?= $l->l_planets; ?></th>
        <th><?= $l->l_ships; ?></th>
    </tr>
    <tr>
        <td><?= implode('<br/>', array_map('htmlspecialchars', $self->planets)); ?></td>
        <td><?= $self->sector === '0' ? $l->l_lrs_zero : implode('<br/>', $self->ships); ?></td>
    </tr>
    <tr>
        <th colspan="2"><?= $l->l_links; ?></th>
    </tr>
    <tr>
        <td colspan="2"><?= $self->links ? implode(', ', array_column($self->links, 'link_dest')) : $l->l_none; ?></td>
    </tr>
    <tr>
        <th colspan="2"><?= $l->l_lss; ?></th>
    </tr>
    <tr>
        <td colspan="2"><?= htmlspecialchars($self->lastShipInSectorDetected) ?></td>
    </tr>
</table>
<a href="<?= route('move', ['sector' => $self->sector]); ?>"><?= $l->l_clickme; ?></a> <?= $l->l_lrs_moveto; ?> <?= $self->sector; ?>.
<?= include_footer(); ?>
