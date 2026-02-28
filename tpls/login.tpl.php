<?php include_header(); ?>
<?php bigtitle(); ?>
<form action="<?= route('login'); ?>" id="bntLoginForm" method="post">
    <div class="mb-3">
        <label for="email" class="form-label"><?= $l->login_email; ?></label>
        <input type="text" name="email" class="form-control" required>
        <div id="emailHelp" class="form-text"></div>
    </div>
    <div class="mb-3">
        <label for="pass" class="form-label"><?= $l->login_pw; ?></label>
        <input type="password" name="pass" class="form-control" required>
    </div>
    <div class="mb-3 row">
        <div class="form-text">
            <?= str_replace('[new]', route('new'), $l->login_newp); ?>
        </div>
    </div>
    <button type="submit" class="btn btn-primary"><?= $l->login_title; ?></button>
</form>
<script type="text/javascript">
    bntForm('bntLoginForm');
</script>
<?php include_footer();?>
