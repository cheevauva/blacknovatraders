<?php $title = $l_warp_title; ?>
<?php include("header.php"); ?>
<?php bigtitle(); ?>
<div class="container">
    <div class="row">
        <div class="col">
            <?php if (empty($links)): ?>
                <div class="alert alert-info">
                    <?php echo $l_warp_nolink; ?>
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <?php echo $l_warp_linkto; ?>
                </div>
                <div class="mb-3">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <?php foreach ($links as $link) : ?>
                            <button type="button" class="btn btn-secondary" disabled><?php echo $link['link_dest']; ?> </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <form action="warpedit.php" method="POST" id="bntWarpeditLink">
                <input type="hidden" name="action" value="link">
                <div class="mb-3">
                    <label class="form-label"><?php echo $l_warp_query; ?></label>
                    <input type="number" name="target_sector" class="form-control" value="" size="6" maxlength="6" required>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input"name="oneway" value="oneway" id="oneway">
                        <label class="form-check-label" for="oneway">
                            <?php echo $l_warp_oneway; ?>?
                        </label>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary" value="<?php echo $l_submit; ?>">
                <input type="reset" class="btn btn-primary" value="<?php echo $l_reset; ?>">
            </form>
        </div>
        <div class="col">
            <form action="warpedit.php" method="POST" id="bntWarpeditUnlink">
                <input type="hidden" name="action" value="unlink">
                <div class="mb-3">
                    <label class="form-label"><?php echo $l_warp_destquery; ?></label>
                    <input type="number" name="target_sector" class="form-control" value="" size="6" maxlength="6" required>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input"name="bothway" value="bothway" id="bothway">
                        <label class="form-check-label" for="bothway">
                            <?php echo $l_warp_bothway; ?>?
                        </label>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary" value="<?php echo $l_submit; ?>">
                <input type="reset" class="btn btn-primary" value="<?php echo $l_reset; ?>">
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    bntForm('bntWarpeditLink');
    bntForm('bntWarpeditUnlink');
</script>
<?php include("footer.php"); ?>
