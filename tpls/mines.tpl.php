<?php $self = BNT\Controller\MinesController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>

<div class="container mt-3">
    <form action="<?= route('mines'); ?>" method="POST" id="bntMinesForm">
        <div class="mb-3">
            <?=
            strtr($l->mines_info1, [
                '[sector]' => $self->playerinfo['sector'],
                '[mines]' => number($self->total_sector_mines),
                '[fighters]' => number($self->total_sector_fighters),
            ]);
            ?>
        </div>

        <div class="mb-3">
            You have <?= number($self->playerinfo['torps']); ?> mines and <?= number($self->playerinfo['ship_fighters']); ?> fighters available to deploy.
        </div>

        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label"><?= $l->mines_deploy; ?></label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="nummines" size="10" maxlength="10" value="<?= $self->playerinfo['torps']; ?>">
            </div>
            <div class="col-sm-6">
                <span class="form-control-plaintext"><?= $l->mines; ?>.</span>
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label"><?= $l->mines_deploy; ?></label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="numfighters" size="10" maxlength="10" value="<?= $self->playerinfo['ship_fighters']; ?>">
            </div>
            <div class="col-sm-6">
                <span class="form-control-plaintext"><?= $l->fighters; ?>.</span>
            </div>
        </div>

        <div class="mb-3 row">
            <label class="col-sm-2 col-form-label"><?= $l->mines_fighter_mode; ?></label>
            <div class="col-sm-10">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="mode" id="modeAttack" value="attack" <?= $self->set_attack ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="modeAttack"><?= $l->mines_att; ?></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="mode" id="modeToll" value="toll" <?= $self->set_toll ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="modeToll"><?= $l->mines_toll; ?></label>
                </div>
            </div>
        </div>

        <div class="mb-3 row">
            <div class="col-sm-10 offset-sm-2">
                <button type="submit" class="btn btn-primary"><?= $l->submit; ?></button>
                <button type="reset" class="btn btn-secondary"><?= $l->reset; ?></button>
            </div>
        </div>

        <input type="hidden" name="op" value="<?= $op; ?>">
    </form>
</div>

<script type="text/javascript">
    bntForm('bntMinesForm');
</script>

<?php include_footer(); ?>
