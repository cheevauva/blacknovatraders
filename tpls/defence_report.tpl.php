<?php $self = \BNT\Controller\DefenceReportController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<?= $l->pr_clicktosort; ?><br><br>

<table class="table table-striped">
    <thead>
        <tr>
            <th><a href="<?= route('defence_report', 'sort=sector'); ?>" ><?= $l->sector; ?></a></th>
            <th><a href="<?= route('defence_report', 'sort=quantity'); ?>" ><?= $l->qty; ?></a></th>
            <th><a href="<?= route('defence_report', 'sort=type'); ?>" ><?= $l->sdf_type; ?></a></th>
            <th><a href="<?= route('defence_report', 'sort=mode'); ?>" ><?= $l->sdf_mode; ?></a></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($self->defences as $defence): ?>
            <tr>
                <td>
                    <a href="<?= route('rsmove', 'engage=1&destination=' . $defence['sector_id']); ?>" class="text-decoration-none">
                        <?= $defence['sector_id']; ?>
                    </a>
                </td>
                <td><?= number_format($defence['quantity']); ?></td>
                <td>
                    <?= $defence['defence_type'] == 'F' ? $l->fighters : $l->mines; ?>
                </td>
                <td>
                    <?php $mode = $defence['defence_type'] == 'F' ? $defence['fm_setting'] : $l->n_a; ?>
                    <?php if ($mode == 'attack'): ?>
                        <?= $l->md_attack; ?>
                    <?php else: ?>
                        <?= $l->md_toll; ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include_footer(); ?>