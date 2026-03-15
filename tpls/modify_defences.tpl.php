<?php $self = BNT\Controller\ModifyDefencesController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<h5><?= $l->t('l_md_consist', ['qty' => $self->qty, 'type' => $self->defenceType, 'owner' => $self->defenceOwner]); ?></h5>
<?php if ($self->defenceinfo['ship_id'] == $self->playerinfo['ship_id']): ?>
    <p><?= $l->l_md_youcan; ?></p>

    <form action="<?= route('modify_defences', ['defence_id' => $self->defenceId]); ?>" method="POST" class="mb-4" id="bntModifyDefencesRetrieveForm">
        <div class="mb-3">
            <label for="quantity" class="form-label"><?= $l->l_md_retrieve; ?></label>
            <input type="text" name="quantity" id="quantity" class="form-control w-auto" size="10" maxlength="10" value="0">
            <small class="text-muted"><?= $self->defenceType; ?></small>
        </div>
        <input type="hidden" name="response" value="retrieve">
        <input type="hidden" name="defence_id" value="<?= $self->defenceId; ?>">
        <button type="submit" class="btn btn-primary"><?= $l->l_submit; ?></button>
    </form>
    <script type="text/javascript">
        bntForm('bntModifyDefencesRetrieveForm');
    </script>

    <?php if ($self->defenceinfo['defence_type'] == 'F'): ?>
        <p><?= $l->l_md_change; ?></p>
        <form action="<?= route('modify_defences', ['defence_id' => $self->defenceId]); ?>" method="POST" id="bntModifyDefencesChangeForm">
            <div class="mb-3">
                <label class="form-label d-block"><?= $l->l_md_cmode; ?></label>
                <div class="form-check form-check-inline">
                    <input type="radio" name="mode" id="mode_attack" class="form-check-input" value="attack" <?php if ($self->defenceinfo['fm_setting'] == 'attack') :?>checked<?php endif;?>>
                    <label for="mode_attack" class="form-check-label"><?= $l->l_md_attack; ?></label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" name="mode" id="mode_toll" class="form-check-input" value="toll" <?php if ($self->defenceinfo['fm_setting'] == 'toll') :?>checked<?php endif;?>>
                    <label for="mode_toll" class="form-check-label"><?= $l->l_md_toll; ?></label>
                </div>
            </div>
            <input type="hidden" name="response" value="change">
            <button type="submit" class="btn btn-primary"><?= $l->l_submit; ?></button>
        </form>
        <script type="text/javascript">
            bntForm('bntModifyDefencesChangeForm');
        </script>
    <?php endif; ?>

<?php else: ?>
    <?php if ($self->fightersOwner['team'] != $self->playerinfo['team'] || $self->playerinfo['team'] == 0): ?>
        <p><?= $l->l_youcan; ?></p>
        <form action="<?= route('modify_defences', ['defence_id' => $self->defenceId]); ?>" method="POST" id="bntModifyDefencesFightForm">
            <p><?= $l->l_md_attdef; ?></p>
            <input type="hidden" name="response" value="fight">
            <button type="submit" class="btn btn-danger"><?= $l->l_md_attack; ?></button>
        </form>
    <?php endif; ?>
    <script type="text/javascript">
        bntForm('bntModifyDefencesFightForm');
    </script>
<?php endif; ?>
<?php include_footer(); ?>
