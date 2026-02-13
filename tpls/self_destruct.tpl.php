<?php $title = $l_login_title; ?>
<?php include("header.php"); ?>
<?php bigtitle(); ?>
<?php if ($sure === 0) : ?>
    <div class="mb-3">
        <?php echo $l_die_rusure; ?>
    </div>
    <div class="mb-3">
        <a href="index.php" class="btn btn-primary"><?php echo $l_die_nonono; ?></a> <?php echo $l_die_what; ?>
    </div>
    <div class="mb-3">
        <a href="self_destruct.php?sure=1" class="btn btn-danger"><?php echo $l_yes; ?>!</a> <?php echo $l_die_goodbye; ?></a>
    </div>
<?php elseif ($sure == 1) : ?>
    <form action="self_destruct.php" id="bntSelfDestructForm" method="POST">
        <input type="hidden" name="sure" value="2">
        <div class="mb-3">
            <?php echo $l_die_rusure; ?>
        </div>
        <div class="mb-3">
            <a href="index.php" class="btn btn-primary"><?php echo $l_die_nonono; ?></a> <?php echo $l_die_what; ?>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-danger"><?php echo $l_yes; ?>!</button> <?php echo $l_die_goodbye; ?>
        </div>
    </form>
    <script type="text/javascript">
        bntForm('bntSelfDestructForm');
    </script>
<?php endif; ?>
<?php include("footer.php"); ?>
