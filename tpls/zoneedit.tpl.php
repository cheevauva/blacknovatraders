<?php $self = \BNT\Controller\ZoneeditController::as($self);?>
<?php include_header();?>
<?php bigtitle(); ?>
<form action="zoneedit.php?command=change&zone=<?= $self->zone; ?>" method="post" id="bntZoneeditForm">
    <div class="mb-3">
        <label for="name" class="form-label"><?= $l->ze_name; ?></label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($self->currentZone['zone_name']); ?>" required>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?= $l->allow . $l->beacons; ?></label>
        <select name="beacons" class="form-control">
            <?php options(['Y' => $l->yes, 'N' => $l->no, 'L' => $l->zi_limit], $self->currentZone['allow_beacon']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?= $l->ze_attacks; ?></label>
        <select name="attacks" class="form-control">
            <?php options(['Y' => $l->yes, 'N' => $l->no], $self->currentZone['allow_attack']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?= $l->allow . $l->warpedit; ?></label>
        <select name="warpedits" class="form-control">
            <?php options(['Y' => $l->yes, 'N' => $l->no, 'L' => $l->zi_limit], $self->currentZone['allow_warpedit']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?= $l->allow . $l->sector_def; ?></label>
        <select name="defenses" class="form-control">
            <?php options(['Y' => $l->yes, 'N' => $l->no, 'L' => $l->zi_limit], $self->currentZone['allow_defenses']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?= $l->ze_genesis; ?></label>
        <select name="planets" class="form-control">
            <?php options(['Y' => $l->yes, 'N' => $l->no, 'L' => $l->zi_limit], $self->currentZone['allow_planet']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?= $l->title_port; ?></label>
        <select name="trades" class="form-control">
            <?php options(['Y' => $l->yes, 'N' => $l->no, 'L' => $l->zi_limit], $self->currentZone['allow_trade']); ?>
        </select>
    </div>

    <input class="btn btn-primary" type="submit" value="<?= $l->submit; ?>">
</form>
<script type="text/javascript">
    bntForm('bntZoneeditForm');
</script>
<?php include_footer();?>
