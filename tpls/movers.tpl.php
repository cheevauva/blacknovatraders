<?php $self = BNT\Controller\MoveRSController::as($self); ?>
<?php global $sector_max; ?>
<?= include_header(); ?>
<?= bigtitle(); ?>
<?php if ($self->engage === 0) : ?>  
    <form action="<?= route('rsmove'); ?>" method="get">
        <p><?= $l->t('l_rs_insector', ['sector' => $self->playerinfo['sector'], 'sector_max' => $sector_max]); ?></p>

        <div class="mb-3">
            <label for="sector" class="form-label"><?= $l->l_rs_whichsector; ?></label>
            <input type="text" name="sector" id="sector" class="form-control w-auto" size="10" maxlength="10">
            <input type="hidden" name="engage" value="1"/>
        </div>

        <button type="submit" class="btn btn-primary"><?= $l->l_rs_submit; ?></button>
    </FORM>
<?php endif; ?>
<?php if ($self->engage >= 1) : ?>  
    <form action="<?= route('rsmove', ['sector' => $self->sector, 'engage' => 2]); ?>" method="post">
        <div class="alert alert-info" role="alert">
            <?= $l->t(['l_rs_movetime', 'l_rs_energy'], ['triptime' => $self->turns, 'energy' => $self->energyScooped]); ?>
        </div>
        <div class="alert alert-warning" role="alert">
            <?= $l->t('l_rs_engage', ['turns' => $self->playerinfo['turns']]); ?>
        </div>
        <button type="submit" class="btn btn-primary"><?= $l->l_rs_engage_link; ?></button>
    </FORM>
<?php endif; ?>

<?= include_footer(); ?>
