<?php $title = $l->beacon_title; ?>
<?php $self = \BNT\Controller\BeaconController::as($this);?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<?php if (!empty($ex)) : ?>
    <div class="alert alert-warning">
        <?php echo $ex->getMessage(); ?>
    </div>
<?php else : ?>
    <div class="alert alert-info">
        <?php if (!empty($self->sectorinfo['beacon'])) : ?>
            <?php echo $l->beacon_reads; ?>: "<?php echo htmlspecialchars($self->sectorinfo['beacon']); ?>"
        <?php else : ?>
            <?php echo $l->beacon_none; ?><br><br>
        <?php endif; ?>
    </div>

    <form action="beacon.php" method="post" id="bntBeacontForm">
        <div class="mb-3">
            <label class="form-label"><?php echo $l->beacon_name; ?></label>
            <input type="text" name="beacon_text" class="form-control" value="" size="40" maxlength="80" >
        </div>
        <input type="submit" class="btn btn-primary" value="<?php echo $l->submit; ?>">
        <input type="reset"  class="btn btn-primary" value="<?php echo $l->reset; ?>">
    </form>
    <script type="text/javascript">
        bntForm('bntBeacontForm');
    </script>
<?php endif; ?>
<?php include_footer(); ?>
