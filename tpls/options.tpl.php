<?php
$title = $l->opt_title;
$self = \BNT\Controller\OptionsController::as($this);
?>
<?php include_header(); ?>
<?php echo bigtitle(); ?>
<form action="options.php" method="POST" id="bntOptionsForm">
    <?php echo $l->opt_chpass; ?>
    <div class="mb-3">
        <label  class="form-label"><?php echo $l->opt_curpass; ?></label>
        <input type="password" name="oldpass" class="form-control" value="">
    </div>
    <div class="mb-3">
        <label  class="form-label"><?php echo $l->opt_newpass; ?></label>
        <input type="password" name="newpass1" class="form-control" value="">
    </div>
    <div class="mb-3">
        <label  class="form-label"><?php echo $l->opt_newpagain; ?></label>
        <input type="password" name="newpass2" class="form-control" value="">
    </div>
    <div class="mb-3 ">
        <label class="form-label"><?php echo $l->opt_lang; ?></label>
        <select name="newlang" class="form-control">
            <?php echo options(languages(), $self->userinfo['lang']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label class="form-label">    <?php echo $l->opt_userint; ?></label>
        <select name="theme" class="form-control">
            <?php echo options(['dark' => 'dark', 'light' => 'light'], $self->userinfo['theme']); ?>
        </select>
    </div>
    <input class="btn btn-primary" type="submit" value="<?php echo $l->opt_save; ?>">
</form>
<script type="text/javascript">
    bntForm('bntOptionsForm');
</script>
<?php include_footer(); ?>
