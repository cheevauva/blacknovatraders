<?php $self = \BNT\Controller\ZoneeditController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<form action="<?= route('zoneedit', ['command' => 'change', 'zone' => $self->zone]); ?>" method="POST" id="bntZoneeditForm">
    <div class="mb-3">
        <label for="name" class="form-label"><?= $l->l_ze_name; ?></label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($self->currentZone['zone_name']); ?>" required>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?= $l->l_allow . $l->l_beacons; ?></label>
        <select name="beacons" class="form-control">
            <?php options(['Y' => $l->l_yes, 'N' => $l->l_no, 'L' => $l->l_zi_limit], $self->currentZone['allow_beacon']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?= $l->l_ze_attacks; ?></label>
        <select name="attacks" class="form-control">
            <?php options(['Y' => $l->l_yes, 'N' => $l->l_no], $self->currentZone['allow_attack']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?= $l->l_allow . $l->l_warpedit; ?></label>
        <select name="warpedits" class="form-control">
            <?php options(['Y' => $l->l_yes, 'N' => $l->l_no, 'L' => $l->l_zi_limit], $self->currentZone['allow_warpedit']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?= $l->l_allow . $l->l_sector_def; ?></label>
        <select name="defenses" class="form-control">
            <?php options(['Y' => $l->l_yes, 'N' => $l->l_no, 'L' => $l->l_zi_limit], $self->currentZone['allow_defenses']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?= $l->l_ze_genesis; ?></label>
        <select name="planets" class="form-control">
            <?php options(['Y' => $l->l_yes, 'N' => $l->l_no, 'L' => $l->l_zi_limit], $self->currentZone['allow_planet']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?= $l->l_title_port; ?></label>
        <select name="trades" class="form-control">
            <?php options(['Y' => $l->l_yes, 'N' => $l->l_no, 'L' => $l->l_zi_limit], $self->currentZone['allow_trade']); ?>
        </select>
    </div>

    <input class="btn btn-primary" type="submit" value="<?= $l->l_submit; ?>">
</form>
<script type="text/javascript">
    bntForm('bntZoneeditForm');
</script>
<?php include_footer(); ?>
