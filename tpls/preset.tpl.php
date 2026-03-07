<?php $self = BNT\Controller\PresetController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<form action="<?= route('preset'); ?>" method="POST" class="preset-form" id="bntPresetForm">
    <div class="mb-3">
        <label for="preset1" class="form-label"><?= $l->l_pre_set_1; ?>:</label>
        <input type="text" name="preset1" id="preset1" class="form-control" size="6" maxlength="6" value="<?= intval($self->playerinfo['preset1']); ?>">
    </div>

    <div class="mb-3">
        <label for="preset2" class="form-label"><?= $l->l_pre_set_2; ?>:</label>
        <input type="text" name="preset2" id="preset2" class="form-control" size="6" maxlength="6" value="<?= intval($self->playerinfo['preset2']); ?>">
    </div>

    <div class="mb-3">
        <label for="preset3" class="form-label"><?= $l->l_pre_set_3; ?>:</label>
        <input type="text" name="preset3" id="preset3" class="form-control" size="6" maxlength="6" value="<?= intval($self->playerinfo['preset3']); ?>">
    </div>

    <input type="hidden" name="change" value="1">
    <button type="submit" class="btn btn-primary"><?php echo $l->l_pre_save; ?></button>
    <script type="text/javascript">
        bntForm('bntPresetForm');
    </script>
</form>
<?php include_footer(); ?>
