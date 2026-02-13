<?php $title = $l_beacon_title; ?>
<?php include 'header.php'; ?>
<?php bigtitle(); ?>
<?php if (!empty($ex)) : ?>
    <div class="alert alert-warning">
        <?php echo $ex->getMessage(); ?>
    </div>
<?php else : ?>
    <div class="alert alert-info">
        <?php if (!empty($sectorinfo['beacon'])) : ?>
            <?php echo $l_beacon_reads; ?>: "<?php echo htmlspecialchars($sectorinfo['beacon']); ?>"
        <?php else : ?>
            <?php echo $l_beacon_none; ?><br><br>
        <?php endif; ?>
    </div>

    <form action="beacon.php" method="post" id="bntBeacontForm">
        <div class="mb-3">
            <label class="form-label"><?php echo $l_beacon_enter; ?></label>
            <input type="text" name="beacon_text" class="form-control" value="" size="40" maxlength="80" required>
        </div>
        <input type="submit" class="btn btn-primary" value="<?php echo $l_submit; ?>">
        <input type="reset"  class="btn btn-primary" value="<?php echo $l_reset; ?>">
    </form>
    <script type="text/javascript">
        bntForm('bntBeacontForm');
    </script>
<?php endif; ?>
<?php include 'footer.php'; ?>
