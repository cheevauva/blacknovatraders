<?php $self = \BNT\Controller\OptionsController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<form action="<?= route('options'); ?>" method="POST" id="bntOptionsForm">
    <?= $l->opt_chpass; ?>
    <div class="mb-3">
        <label  class="form-label"><?= $l->opt_curpass; ?></label>
        <input type="password" name="oldpass" class="form-control" value="">
    </div>
    <div class="mb-3">
        <label  class="form-label"><?= $l->opt_newpass; ?></label>
        <input type="password" name="newpass1" class="form-control" value="">
    </div>
    <div class="mb-3">
        <label  class="form-label"><?= $l->opt_newpagain; ?></label>
        <input type="password" name="newpass2" class="form-control" value="">
    </div>
    <div class="mb-3 ">
        <label class="form-label"><?= $l->opt_lang; ?></label>
        <select name="newlang" class="form-control">
            <?= options(languages(), $self->userinfo['lang']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label class="form-label">    <?= $l->opt_userint; ?></label>
        <select name="theme" class="form-control">
            <?= options(['dark' => 'dark', 'light' => 'light'], $self->userinfo['theme']); ?>
        </select>
    </div>
    <input class="btn btn-primary" type="submit" value="<?= $l->opt_save; ?>">
</form>
<script type="text/javascript">
    bntForm('bntOptionsForm');
</script>
<?php include_footer(); ?>
