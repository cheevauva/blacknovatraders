<?php $self = BNT\Controller\SelfDestructController::as($self);?>
<?php include_header();?>
<?php bigtitle(); ?>
<?php if ($self->sure === 0) : ?>
    <form action="<?= route('self_destruct');?>" id="bntSelfDestructForm" method="POST">
        <input type="hidden" name="sure" value="2">
        <div class="mb-3">
            <?= $l->l_die_rusure; ?>
        </div>
        <div class="mb-3">
            <a href="index.php" class="btn btn-primary"><?= $l->l_die_nonono; ?></a> <?= $l->l_die_what; ?>
        </div>
        <div class="mb-3">
            <button type="submit" class="btn btn-danger"><?= $l->l_yes; ?>!</button> <?= $l->l_die_goodbye; ?>
        </div>
    </form>
    <script type="text/javascript">
        bntForm('bntSelfDestructForm');
    </script>
<?php endif; ?>
<?php include_footer();?>
