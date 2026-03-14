<?php $self = BNT\Controller\LongRangeScanController::as($self); ?>
<?= include_header(); ?>
<?= bigtitle(); ?>
<?= $l->l_lrs_used; ?> <?= number($self->fullscan_cost); ?> <?= $l->l_lrs_turns; ?> <?= number($self->playerinfo['turns']); ?> <?= $l->l_lrs_left; ?><BR><BR>
<?= $l->t('l_lrs_reach', ['sector' => $self->playerinfo['sector']]); ?><BR><BR>
<table class="table table-hover">
    <tr>
        <th><?= $l->l_sector; ?></th>
        <th></th>
        <th><?= $l->l_lrs_links; ?></th>
        <th><?= $l->l_lrs_ships; ?></th>
        <th></th>
        <th><?= $l->l_port; ?></th>
        <th><?= $l->l_planets; ?></th>
        <th><?= $l->l_mines; ?></th>
        <th><?= $l->l_fighters; ?></th>
        <?php if ($self->playerinfo['dev_lssd'] == 'Y') : ?>
            <th><?= $l->l_lss; ?></th>
        <?php endif; ?>
    </tr>
    <?php foreach ($self->links as $link) : ?>
        <tr>
            <td>
                <form action="<?= route('move', ['sector' => $link['link_dest']]); ?>" method="post" class="d-inline">
                    <a href="javascript:;" onclick="parentNode.submit();"><?= $link['link_dest']; ?></a>&nbsp;
                </form>
            <td><a href="<?= route('lrscan_sector', ['sector' => $link['link_dest']]); ?>"><?= $l->l_lrs_scan; ?></a></td>
            <td><?= $link['num_links']; ?></td>
            <td><?= $link['num_ships']; ?></td>
            <td><?php if ($link['port_type'] !== 'none'): ?><img src="images/<?= $link['port_type']; ?>.gif"><?php endif; ?></td>
            <td><?= t_port($link['port_type']); ?></td>
            <td><?= $link['has_planet']; ?></td>
            <td><?= $link['has_mines']; ?></td>
            <td><?= $link['has_fighters']; ?></td>
            <?php if ($self->playerinfo['dev_lssd'] == 'Y') : ?>
                <td><?= $link['lssd_ship_name']; ?></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</table>
<br/>
<?= $l->l_lrs_click; ?>
<?= include_footer(); ?>
