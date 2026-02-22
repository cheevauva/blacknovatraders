<?php $self = BNT\Controller\SelfDestructController::as($self);?>
<?php include_header();?>
<?php bigtitle(); ?>
<?php if ($self->sure === 0) : ?>
    <form action="self_destruct.php" id="bntSelfDestructForm" method="POST">
        <input type="hidden" name="sure" value="2">
        <div class="mb-3">
            <?php echo $l->die_rusure; ?>
        </div>
        <div class="mb-3">
            <a href="index.php" class="btn btn-primary"><?php echo $l->die_nonono; ?></a> <?php echo $l->die_what; ?>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-danger"><?php echo $l->yes; ?>!</button> <?php echo $l->die_goodbye; ?>
        </div>
    </form>
    <script type="text/javascript">
        bntForm('bntSelfDestructForm');
    </script>
<?php endif; ?>
<?php include_footer();?>
