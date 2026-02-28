<?php $self = \BNT\Controller\NewController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<form action="<?= route('new');?>" id="bntNewForm" method="POST">
    <div class="mb-3">
        <div class="form-text">
            <?= $l->new_info; ?>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label"><?= $l->new_username; ?></label>
        <input type="text" name="username" class="form-control" maxlength="40" required>
    </div>
    <div class="mb-3">
        <label class="form-label"><?= $l->new_shipname; ?></label>
        <input type="text" name="shipname" class="form-control" maxlength="20"  required>
    </div>
    <div class="mb-3">
        <label class="form-label"><?= $l->new_pname; ?></label>
        <input type="text" name="character" class="form-control" maxlength="20"  required>
    </div>
    <div class="mb-3">
        <label class="form-label"><?= $l->new_password; ?></label>
        <input type="password" name="password" class="form-control" maxlength="20"  required>
    </div>
    <input type="submit" class="btn btn-primary" value="<?= $l->submit; ?>">
    <input type="reset"  class="btn btn-primary" value="<?= $l->reset; ?>">
</form>
<script type="text/javascript">
    bntForm('bntNewForm');
</script>
<?php include_footer(); ?>
