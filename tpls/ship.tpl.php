<?php $title = $l_ship_title; ?>
<?php include("header.php"); ?>
<?php bigtitle(); ?>
<?php if ($othership['sector'] != $playerinfo['sector']) : ?>
    <div class="alert alert-info">
        <?php echo $l_ship_the; ?> <?php echo $othership['ship_name']; ?> <?php echo $l_ship_nolonger; ?> <?php echo $playerinfo['sector']; ?>
    </div>
<?php else : ?>
    <div class="mb-3">
        <div class="alert alert-warning">
            <?php echo $l_ship_youc; ?> 
            <?php echo htmlspecialchars($othership['ship_name']); ?>, 
            <?php echo $l_ship_owned; ?> 
            <?php echo htmlspecialchars($othership['ship_name']); ?>.
        </div>
    </div>
    <div class="mb-3">
        <?php echo $l_ship_perform; ?>
    </div>
    <div class="mb-3">
        <a class="btn btn-secondary" href="scan.php?ship_id=<?php echo $ship_id; ?>" ><?php echo $l_planet_scn_link; ?></a>
    </div>
    <div class="mb-3">
        <a class="btn btn-danger" href="attack.php?ship_id=<?php echo $ship_id; ?>"><?php echo $l_planet_att_link; ?></a>
    </div>
    <div class="mb-3">
        <a class="btn btn-info" href="mailto.php?to=<?php echo $ship_id; ?>"><?php echo $l_send_msg; ?></a>
    </div>
<?php endif; ?>

<?php include("footer.php"); ?>
