<?php include "header.php"; ?>
<?php $title = $l_ze_title; ?>
<?php bigtitle(); ?>
<form action="zoneedit.php?command=change&zone=<?php echo $zone; ?>" method="post" id="bntZoneeditForm">
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label"><?php echo $l_ze_name; ?></label>
        <input type="text" name="name" class="form-control" aria-describedby="emailHelp" value="<?php echo htmlspecialchars($curzone['zone_name']); ?>" required>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?php echo $l_allow . $l_beacons; ?></label>
        <select name="beacons" class="form-control">
            <?php options(['Y' => $l_yes, 'N' => $l_no, 'L' => $l_zi_limit], $curzone['allow_beacon']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?php echo $l_ze_attacks; ?></label>
        <select name="attacks" class="form-control">
            <?php options(['Y' => $l_yes, 'N' => $l_no], $curzone['allow_attack']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?php echo $l_allow . $l_warpedit; ?></label>
        <select name="warpedits" class="form-control">
            <?php options(['Y' => $l_yes, 'N' => $l_no, 'L' => $l_zi_limit], $curzone['allow_warpedit']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?php echo $l_allow . $l_sector_def; ?></label>
        <select name="defenses" class="form-control">
            <?php options(['Y' => $l_yes, 'N' => $l_no, 'L' => $l_zi_limit], $curzone['allow_defenses']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?php echo $l_ze_genesis; ?></label>
        <select name="planets" class="form-control">
            <?php options(['Y' => $l_yes, 'N' => $l_no, 'L' => $l_zi_limit], $curzone['allow_planet']); ?>
        </select>
    </div>
    <div class="mb-3 ">
        <label for="exampleInputPassword1" class="form-label"><?php echo $l_title_port; ?></label>
        <select name="trades" class="form-control">
            <?php options(['Y' => $l_yes, 'N' => $l_no, 'L' => $l_zi_limit], $curzone['allow_trade']); ?>
        </select>
    </div>

    <input class="btn btn-primary" type="submit" value="<?php echo $l_submit; ?>">
</form>
<script type="text/javascript">
    bntForm('bntZoneeditForm');
</script>
<?php include "footer.php"; ?>
