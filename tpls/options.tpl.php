<?php $title = $l_opt_title; ?>
<?php include "header.php"; ?>
<?php echo bigtitle(); ?>
<form action="options.php" method="POST" id="bntOptionsForm">
    <?php echo $l_opt_chpass; ?>
    <div class="mb-3">
        <label  class="form-label"><?php echo $l_opt_curpass; ?></label>
        <input type="password" name="oldpass" class="form-control" value="">
    </div>
    <div class="mb-3">
        <label  class="form-label"><?php echo $l_opt_newpass; ?></label>
        <input type="password" name="newpass1" class="form-control" value="">
    </div>
    <div class="mb-3">
        <label  class="form-label"><?php echo $l_opt_newpagain; ?></label>
        <input type="password" name="newpass2" class="form-control" value="">
    </div>
    <?php echo $l_opt_userint; ?>
    <div class="mb-3 ">
        <label class="form-label"><?php echo $l_ze_genesis; ?></label>
        <select name="newlang" class="form-control">
            <?php echo options(languages(), $playerinfo['lang']); ?>
        </select>
    </div>
    <input class="btn btn-primary" type="submit" value="<?php echo $l_opt_save; ?>">
</form>
<script type="text/javascript">
    bntForm('bntOptionsForm');
</script>
<?php include "footer.php"; ?>
